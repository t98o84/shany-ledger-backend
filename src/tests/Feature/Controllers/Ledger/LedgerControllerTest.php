<?php

namespace Tests\Feature\Controllers\Ledger;

use App\Events\Ledger\LedgerCreated;
use App\Models\Ledger\Ledger;
use App\ValueObjects\Ledger\LedgerPublicStatus;
use App\ValueObjects\Ledger\LedgerUnitDisplayPosition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Resources\Shared\OperatorResource;
use Tests\Feature\Traits\HasWorkspace;
use Tests\TestCase;

class LedgerControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, HasWorkspace;

    private Ledger $ledger;

    protected function setUp(): void
    {
        parent::setUp();

        \Storage::fake();
        \Event::fake([LedgerCreated::class]);
        $this->initWorkspace();
        $this->ledger = $this->workspace->ledgers()->create(Ledger::factory()->make()->toArray());
    }

    public function testCreate_ValidData_StoredLedgerResponse(): void
    {
        Sanctum::actingAs($this->owner);

        $response = $this->postJson(route('ledger.store', ['workspace' => $this->workspace->id]), [
            'name' => 'test name',
            'description' => 'test description',
            'public_status' => LedgerPublicStatus::WorkspaceParticipant->value,
            'unit' => [
                'symbol' => 'US',
                'display_position' => LedgerUnitDisplayPosition::Left->value,
                'description' => 'unit description',
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJson(fn(AssertableJson $json) => $json
                ->whereType('id', 'string')
                ->where('name', 'test name')
                ->where('description', 'test description')
                ->where('public_status', LedgerPublicStatus::WorkspaceParticipant->value)
                ->has('unit', fn(AssertableJson $json) => $json
                    ->where('symbol', 'US')
                    ->where('display_position', LedgerUnitDisplayPosition::Left->value)
                    ->where('description', 'unit description')
                   ->etc()
                )
                ->has('workspace', fn(AssertableJson $json) => $json
                    ->where('id', $this->workspace->id)
                    ->where('url', $this->workspace->url)
                    ->where('name', $this->workspace->name)
                    ->where('description', $this->workspace->description)
                    ->etc()
                )
                ->whereType('created_at', 'string')
                ->whereType('updated_at', 'string')
                ->has('creator', OperatorResource::json($this->owner))
            );
    }

    public function testUpdate_ValidData_UpdatedLedgerResponse(): void
    {
        Sanctum::actingAs($this->owner);

        $response = $this->patchJson(route('ledger.update', ['workspace' => $this->workspace->id, 'ledger' => $this->ledger->id]), [
            'name' => 'Update new name',
            'description' => 'Update new description',
        ]);

        $response
            ->assertOK()
            ->assertJson(fn(AssertableJson $json) => $json
                ->whereType('id', 'string')
                ->where('name', 'Update new name')
                ->where('description', 'Update new description')
                ->where('public_status', LedgerPublicStatus::WorkspaceParticipant->value)
                ->has('workspace', fn(AssertableJson $json) => $json
                    ->where('id', $this->workspace->id)
                    ->where('url', $this->workspace->url)
                    ->where('name', $this->workspace->name)
                    ->where('description', $this->workspace->description)
                    ->etc()
                )
                ->whereType('created_at', 'string')
                ->whereType('updated_at', 'string')
                ->has('updater', OperatorResource::json($this->owner))
            );
    }
}
