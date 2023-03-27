<?php

namespace Tests\Feature\Actions\Ledger;

use App\Actions\Ledger\UpdateLedger;
use App\Events\Ledger\LedgerUpdated;
use App\Models\Ledger\Ledger;
use App\Models\Workspace\WorkspaceAccount;
use App\Models\Workspace\WorkspaceAccountRole;
use App\Requests\Ledger\UpdateLedgerRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Traits\HasWorkspace;
use Tests\TestCase;

class UpdateLedgerTest extends TestCase
{
    use RefreshDatabase, WithFaker, HasWorkspace;

    private readonly Ledger $ledger;

    private readonly UpdateLedger $updateLedger;

    protected function setUp(): void
    {
        parent::setUp();

        \Storage::fake();
        \Event::fake([LedgerUpdated::class]);
        $this->initWorkspace();
        $this->ledger = $this->workspace->ledgers()->create(Ledger::factory()->make()->toArray());
        $this->updateLedger = new UpdateLedger();
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_Unauthenticated_AuthenticationExceptionThrown(): void
    {
        $this->expectException(\Illuminate\Auth\AuthenticationException::class);
        $this->updateLedger->handle($this->makeUpdateLedgerRequest());
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_LedgerUpdated(): void
    {
        Sanctum::actingAs($this->owner);
        $request = $this->makeUpdateLedgerRequest();
        $ledger = $this->updateLedger->handle($request);

        $this->assertDatabaseHas(Ledger::class, [
            'id' => $ledger->id,
            'name' => $request->ledger->name,
            'description' => $request->ledger->description,
        ]);

        $this->assertSame( $request->ledger->name, $ledger->name);
        $this->assertSame($request->ledger->description, $ledger->description);
        \Event::assertDispatched(LedgerUpdated::class);
    }

    /**
     * @dataProvider authorizedWorkspaceAccountRoleProvider
     * @throws \Throwable
     */
    public function testHandle_Authorized_LedgerUpdated(WorkspaceAccountRole $role): void
    {
        $user = WorkspaceAccount::factory()->hasUser()->create(['workspace_id' => $this->workspace->id, 'role' => $role->value])->user;
        Sanctum::actingAs($user);

        $ledger = $this->updateLedger->handle($this->makeUpdateLedgerRequest());

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
        $this->updateLedger->handle($this->makeUpdateLedgerRequest());
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
        $this->updateLedger->handle($this->makeUpdateLedgerRequest());
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

    private function makeUpdateLedgerRequest(): UpdateLedgerRequest
    {
        return UpdateLedgerRequest::make([
            'id' => $this->ledger->id,
            'workspace_id' => $this->ledger->workspace->id,
            'name' => 'Update new name',
            'description' => 'Update new description',
        ]);
    }
}
