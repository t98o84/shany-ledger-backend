<?php

namespace App\Actions\Ledger;

use App\Events\Ledger\LedgerPublicStatusUpdated;
use App\Models\Ledger\Ledger;
use App\Requests\Ledger\UpdateLedgerPublicStatusRequest;
use App\ValueObjects\Ledger\LedgerPublicStatus;

class UpdateLedgerPublicStatus
{
    public function handle(UpdateLedgerPublicStatusRequest $request): Ledger
    {
        return \DB::transaction(function () use ($request) {
            $ledger = clone $request->ledger;
            $ledger->save();

            if ($ledger->public_status === LedgerPublicStatus::Anyone) {
                $ledgerPublicStatusAnyoneSetting = clone $request->ledgerPublicStatusAnyoneSetting;
                $ledgerPublicStatusAnyoneSetting->save();
                $ledger->setRelation('public_status_anyone_setting', $ledgerPublicStatusAnyoneSetting);
            } else {
                $ledger->publicStatusAnyoneSetting?->delete();
            }

            LedgerPublicStatusUpdated::dispatch($ledger, $request->workspaceAccount);

            return $ledger;
        });
    }
}
