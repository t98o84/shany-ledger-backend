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

class LedgerPublicStatusControllerTest extends TestCase
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

    public function testUpdate_ValidData_UpdatedLedgerPublicStatusResponse(): void
    {
        Sanctum::actingAs($this->owner);

        $response = $this->patchJson(route('ledger.public_status.update', ['workspace' => $this->workspace->id, 'ledger' => $this->ledger->id]), [
            'public_status' => LedgerPublicStatus::WorkspaceParticipant->value,
        ]);

        $response
            ->assertOK()
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('status', LedgerPublicStatus::WorkspaceParticipant->value)
            );
    }

    public function testUpdate_AnyoneStatus_AnyoneSettingsResponse(): void
    {
        Sanctum::actingAs($this->owner);

        $response = $this->patchJson(route('ledger.public_status.update', ['workspace' => $this->workspace->id, 'ledger' => $this->ledger->id]), [
            'public_status' => LedgerPublicStatus::Anyone->value,
            'anyone_settings' => [
                'url' => 'test-url',
                'allow_comments' => true,
                'allow_editing' => true,
                'allow_duplicate' => true,
                'expiration_started_at' => '2000-01-01 00:00:00',
                'expiration_ended_at' => '9999-12-31 23:59:59',
            ],
        ]);

        $response
            ->assertOK()
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('status', LedgerPublicStatus::Anyone->value)
                ->has('anyone_settings', fn(AssertableJson $json) => $json
                    ->whereType('url', 'string')
                    ->where('allow_comments', true)
                    ->where('allow_editing', true)
                    ->where('allow_duplicate', true)
                    ->where('expiration_started_at', '2000-01-01T00:00:00.000000Z')
                    ->where('expiration_ended_at', '9999-12-31T23:59:59.000000Z')
                    ->etc()
                )
            );
    }
}
