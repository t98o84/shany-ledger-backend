<?php

namespace App\Actions\Ledger;

use App\Events\Ledger\LedgerUpdated;
use App\Models\Ledger\Ledger;
use App\Requests\Ledger\UpdateLedgerRequest;

class UpdateLedger
{
    public function handle(UpdateLedgerRequest $request): Ledger
    {
        $ledger = clone $request->ledger;
        $ledger->save();

        LedgerUpdated::dispatch($ledger, $request->workspaceAccount);

        return $ledger;
    }
}
