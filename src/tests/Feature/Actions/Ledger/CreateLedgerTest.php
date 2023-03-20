<?php

namespace Tests\Feature\Actions\Ledger;

use App\Actions\Ledger\CreateLedger;
use App\Actions\Ledger\LedgerErrorCode;
use App\Events\Ledger\LedgerCreated;
use App\Models\Ledger\Ledger;
use App\Models\Ledger\LedgerPublicStatusAnyoneSetting;
use App\Models\Ledger\LedgerUnit;
use App\Models\Workspace\WorkspaceAccount;
use App\Models\Workspace\WorkspaceAccountRole;
use App\Requests\Ledger\CreateLedgerRequest;
use App\ValueObjects\Ledger\LedgerPublicStatus;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Traits\HasWorkspace;
use Tests\TestCase;

class CreateLedgerTest extends TestCase
{
    use RefreshDatabase, WithFaker, HasWorkspace;

    private readonly CreateLedger $createLedger;

    protected function setUp(): void
    {
        parent::setUp();

        \Storage::fake();
        \Event::fake([LedgerCreated::class]);
        $this->initWorkspace();
        $this->createLedger = new CreateLedger();
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_Unauthenticated_AuthenticationExceptionThrown(): void
    {
        $this->expectException(\Illuminate\Auth\AuthenticationException::class);
        $this->createLedger->handle($this->makeCreateLedgerRequest());
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_LedgerCreated(): void
    {
        Sanctum::actingAs($this->owner);
        $request = $this->makeCreateLedgerRequest();
        $ledger = $this->createLedger->handle($request);

        $this->assertDatabaseHas(Ledger::class, [
            'id' => $ledger->id,
            'name' => $request->ledger->name,
            'description' => $request->ledger->description,
            'public_status' => $request->ledger->public_status->value,
            'workspace_id' => $this->workspace->id,
        ]);
        $this->assertDatabaseHas(LedgerUnit::class, [
            'ledger_id' => $ledger->id,
            'symbol' => $request->ledgerUnit->symbol,
            'display_position' => $request->ledgerUnit->display_position->value,
        ]);
        $this->assertDatabaseMissing(LedgerPublicStatusAnyoneSetting::class, [
            'ledger_id' => $ledger->id,
        ]);

        $this->assertInstanceOf(Ledger::class, $ledger);
        $this->assertSame($request->ledger->name, $ledger->name);
        $this->assertSame($request->ledger->description, $ledger->description);
        $this->assertSame($request->ledger->public_status, $ledger->public_status);
        $this->assertSame($this->workspace->id, $ledger->workspace_id);
        $this->assertSame($request->ledgerUnit->symbol, $ledger->unit->symbol);
        $this->assertSame($request->ledgerUnit->display_position, $ledger->unit->display_position);
        \Event::assertDispatched(LedgerCreated::class);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_PublicStatusIsAnyone_LedgerPublicStatusAnyoneSettingCreated(): void
    {
        Sanctum::actingAs($this->owner);
        $request = $this->makeCreateLedgerRequest(['public_status' => LedgerPublicStatus::Anyone->value]);
        $ledger = $this->createLedger->handle($request);

        $this->assertDatabaseHas(LedgerPublicStatusAnyoneSetting::class, [
            'ledger_id' => $ledger->id,
            'allow_comments' => false,
            'allow_editing' => false,
            'allow_duplicate' => false,
            'expiration_started_at' => null,
            'expiration_ended_at' => null,
        ]);

        $this->assertInstanceOf(LedgerPublicStatusAnyoneSetting::class, $ledger->public_status_anyone_setting);
        $this->assertFalse($ledger->public_status_anyone_setting->allow_comments);
        $this->assertFalse($ledger->public_status_anyone_setting->allow_editing);
        $this->assertFalse($ledger->public_status_anyone_setting->allow_duplicate);
        $this->assertNull($ledger->public_status_anyone_setting->expiration_started_at);
        $this->assertNull($ledger->public_status_anyone_setting->expiration_ended_at);
    }

    /**
     * @dataProvider authorizedWorkspaceAccountRoleProvider
     * @throws \Throwable
     */
    public function testHandle_Authorized_LedgerCreated(WorkspaceAccountRole $role): void
    {
        $user = WorkspaceAccount::factory()->hasUser()->create(['workspace_id' => $this->workspace->id, 'role' => $role->value])->user;
        Sanctum::actingAs($user);

        $ledger = $this->createLedger->handle($this->makeCreateLedgerRequest());

        $this->assertInstanceOf(Ledger::class, $ledger);
    }

    /**
     * @dataProvider unauthorizedWorkspaceAccountRoleProvider
     * @throws \Throwable
     */
    public function testHandle_Unauthorized_AuthorizationExceptionThrown(WorkspaceAccountRole $role): void
    {
        $user = WorkspaceAccount::factory()->hasUser()->create(['workspace_id' => $this->workspace->id, 'role' => $role->value])->user;
        Sanctum::actingAs($user);

        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);
        $this->createLedger->handle($this->makeCreateLedgerRequest());
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_CreateTowLedger_TooManyLedgersReturned(): void
    {
        $max = 5;
        Sanctum::actingAs($this->owner);
        foreach (range(1, $max) as $_) {
            $ledger = $this->createLedger->handle($this->makeCreateLedgerRequest());
            $this->assertInstanceOf(Ledger::class, $ledger);
        }

        $this->assertDatabaseCount(Ledger::class, $max);

        $error = $this->createLedger->handle($this->makeCreateLedgerRequest());

        $this->assertSame(LedgerErrorCode::TooManyLedgers, $error);
    }

    public function unauthorizedWorkspaceAccountRoleProvider(): array
    {
        return [
            WorkspaceAccountRole::Viewer->value => [WorkspaceAccountRole::Viewer],
            WorkspaceAccountRole::Guest->value => [WorkspaceAccountRole::Guest],
        ];
    }

    public function authorizedWorkspaceAccountRoleProvider(): array
    {
        return [
            WorkspaceAccountRole::Administrator->value => [WorkspaceAccountRole::Administrator],
            WorkspaceAccountRole::Editor->value => [WorkspaceAccountRole::Editor],
        ];
    }

    /**
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws ValidationException
     */
    private function makeCreateLedgerRequest(array $params = []): CreateLedgerRequest
    {
        $ledger = Ledger::factory()->make(['workspace_id' => $this->workspace->id]);
        $unit = LedgerUnit::factory()->make(['ledger_id' => $ledger->id]);
        return CreateLedgerRequest::make([
            ...$ledger->toArray(),
            'unit' => $unit->toArray(),
            ...$params
        ]);
    }
}
