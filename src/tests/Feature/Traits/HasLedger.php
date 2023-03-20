<?php

namespace Tests\Feature\Traits;

use App\Models\Ledger\Ledger;

trait HasLedger
{
    use HasWorkspace;

    protected readonly Ledger $ledger;

    protected function initWorkspace(): void
    {
        $this->ledger = Ledger::factory()->create(['workspace_id' => $this->workspace->id, 'created_by' => $this->owner->id]);
    }
}
