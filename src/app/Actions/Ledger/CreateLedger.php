<?php

namespace App\Actions\Ledger;

use App\Events\Ledger\LedgerCreated;
use App\Models\Ledger\Ledger;
use App\Requests\Ledger\CreateLedgerRequest;
use App\ValueObjects\Ledger\LedgerPublicStatus;

class CreateLedger
{
    /**
     * @throws \Throwable
     */
    public function handle(CreateLedgerRequest $request): Ledger|LedgerErrorCode
    {
        \DB::beginTransaction();
        try {
            $ledgerNum = Ledger::where('workspace_id', $request->workspace->id)->lockForUpdate()->count();
            if ($ledgerNum >= 5) { // TODO: プランに応じて変更する
                \DB::rollBack();
                return LedgerErrorCode::TooManyLedgers;
            }

            $ledger = clone $request->ledger;
            $request->workspace->ledgers()->save($ledger);
            $unit = clone $request->ledgerUnit;
            $ledger->unit()->save($unit);
            $ledger->setRelation('unit', $unit);

            if ($ledger->public_status === LedgerPublicStatus::Anyone) {
                $urlPrefix = '-' . \Str::uuid();
                $publicStatusAnyoneSetting = $ledger->publicStatusAnyoneSetting()->create(['url' => substr(urlencode($ledger->name), 100 - strlen($urlPrefix)) . $urlPrefix]);
                $ledger->setRelation('public_status_anyone_setting', $publicStatusAnyoneSetting);
            }

            if ($request->detailSetting || $this->type->detailSettingModel()) {
                $detailSetting = $request->detailSetting
                    ? clone $request->detailSetting
                    : $this->type->detailSettingModel()::make();
                $ledger->detailSetting()->save($detailSetting);
                $ledger->setRelation('detail_setting', $detailSetting);
            }

            \DB::commit();

            LedgerCreated::dispatch($ledger, $request->workspaceAccount);

            return $ledger;
        } catch (\Throwable $throwable) {
            \DB::rollBack();
            throw $throwable;
        }
    }
}
