<?php

namespace Tests\Feature\Actions\Ledger;

use App\Actions\Ledger\UpdateLedgerPublicStatus;
use App\Events\Ledger\LedgerPublicStatusUpdated;
use App\Events\Ledger\LedgerUpdated;
use App\Models\Ledger\Ledger;
use App\Models\Ledger\LedgerPublicStatusAnyoneSetting;
use App\Models\Workspace\WorkspaceAccount;
use App\Models\Workspace\WorkspaceAccountRole;
use App\Requests\Ledger\UpdateLedgerPublicStatusRequest;
use App\ValueObjects\Ledger\LedgerPublicStatus;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Traits\HasWorkspace;
use Tests\TestCase;

class UpdateLedgerPublicStatusTest extends TestCase
{
    use RefreshDatabase, WithFaker, HasWorkspace;

    private readonly Ledger $ledger;

    private readonly UpdateLedgerPublicStatus $action;

    protected function setUp(): void
    {
        parent::setUp();

        \Storage::fake();
        \Event::fake([LedgerPublicStatusUpdated::class]);
        $this->initWorkspace();
        $this->ledger = $this->workspace->ledgers()->create(Ledger::factory()->make()->toArray());
        $this->action = new UpdateLedgerPublicStatus();
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_Unauthenticated_AuthenticationExceptionThrown(): void
    {
        $this->expectException(\Illuminate\Auth\AuthenticationException::class);
        $this->action->handle($this->makeRequestForLedgerParticipant());
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_LedgerPublicStatusUpdated(): void
    {
        Sanctum::actingAs($this->owner);

        $this->assertDatabaseHas(Ledger::class, [
            'id' => $this->ledger->id,
            'public_status' => LedgerPublicStatus::WorkspaceParticipant->value,
        ]);

        $request = $this->makeRequestForLedgerParticipant();
        $ledger = $this->action->handle($request);

        $this->assertDatabaseHas(Ledger::class, [
            'id' => $ledger->id,
            'public_status' => $request->ledger->public_status->value,
        ]);

        $this->assertSame($request->ledger->public_status, $ledger->public_status);
        \Event::assertDispatched(LedgerPublicStatusUpdated::class);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_AnyoneStatus_LedgerPublicStatusAnyoneSettingUpdated(): void
    {
        Sanctum::actingAs($this->owner);

        $this->assertDatabaseHas(Ledger::class, [
            'id' => $this->ledger->id,
            'public_status' => LedgerPublicStatus::WorkspaceParticipant->value,
        ]);

        $request = $this->makeRequestForAnyone();
        $ledger = $this->action->handle($request);

        $this->assertDatabaseHas(Ledger::class, [
            'id' => $ledger->id,
            'public_status' => $request->ledger->public_status->value,
        ]);

        $this->assertDatabaseHas(LedgerPublicStatusAnyoneSetting::class, [
            'ledger_id' => $ledger->id,
            'url' => $request->ledgerPublicStatusAnyoneSetting->url,
            'allow_comments' => $request->ledgerPublicStatusAnyoneSetting->allow_comments,
            'allow_editing' => $request->ledgerPublicStatusAnyoneSetting->allow_editing,
            'allow_duplicate' => $request->ledgerPublicStatusAnyoneSetting->allow_duplicate,
            'expiration_started_at' => $request->ledgerPublicStatusAnyoneSetting->expiration_started_at,
            'expiration_ended_at' => $request->ledgerPublicStatusAnyoneSetting->expiration_ended_at,
        ]);

        $this->assertSame($request->ledger->public_status, $ledger->public_status);
        $this->assertSame($request->ledgerPublicStatusAnyoneSetting->url, $ledger->public_status_anyone_setting->url);
        $this->assertSame($request->ledgerPublicStatusAnyoneSetting->allow_comments, $ledger->public_status_anyone_setting->allow_comments);
        $this->assertSame($request->ledgerPublicStatusAnyoneSetting->allow_editing, $ledger->public_status_anyone_setting->allow_editing);
        $this->assertSame($request->ledgerPublicStatusAnyoneSetting->allow_duplicate, $ledger->public_status_anyone_setting->allow_duplicate);
        $this->assertSame($request->ledgerPublicStatusAnyoneSetting->expiration_started_at, $ledger->public_status_anyone_setting->expiration_started_at);
        $this->assertSame($request->ledgerPublicStatusAnyoneSetting->expiration_ended_at, $ledger->public_status_anyone_setting->expiration_ended_at);
        \Event::assertDispatched(LedgerPublicStatusUpdated::class);
    }


    /**
     * @dataProvider authorizedWorkspaceAccountRoleProvider
     * @throws \Throwable
     */
    public function testHandle_Authorized_LedgerUpdated(WorkspaceAccountRole $role): void
    {
        $user = WorkspaceAccount::factory()->hasUser()->create(['workspace_id' => $this->workspace->id, 'role' => $role->value])->user;
        Sanctum::actingAs($user);

        $ledger = $this->action->handle($this->makeRequestForLedgerParticipant());

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
        $this->action->handle($this->makeRequestForLedgerParticipant());
    }

    /**
     * @dataProvider authorizedWorkspaceAccountRoleProvider
     * @throws \Throwable
     */
    public function testHandle_NoWorkspaceAccount_AuthorizationExceptionThrown(WorkspaceAccountRole $role): void
    {
        $user = WorkspaceAccount::factory()->hasUser()->hasWorkspace()->create(['role' => $role->value])->user;
        Sanctum::actingAs($user);

        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);
        $this->action->handle($this->makeRequestForLedgerParticipant());
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
     * @throws AuthorizationException
     * @throws AuthenticationException
     * @throws ValidationException
     */
    private function makeRequestForLedgerParticipant(): UpdateLedgerPublicStatusRequest
    {
        return UpdateLedgerPublicStatusRequest::make([
            'id' => $this->ledger->id,
            'workspace_id' => $this->ledger->workspace->id,
            'public_status' => LedgerPublicStatus::LedgerParticipant->value,
        ]);
    }

    /**
     * @throws AuthorizationException
     * @throws AuthenticationException
     * @throws ValidationException
     */
    private function makeRequestForAnyone(): UpdateLedgerPublicStatusRequest
    {
        return UpdateLedgerPublicStatusRequest::make([
            'id' => $this->ledger->id,
            'workspace_id' => $this->ledger->workspace->id,
            'public_status' => LedgerPublicStatus::Anyone->value,
            'anyone_settings' => LedgerPublicStatusAnyoneSetting::factory()->make()->toArray()
        ]);
    }
}
