<?php

namespace App\Models\Ledger;

use App\Http\Resources\Ledger\LedgerAggregationSettingResource;

enum LedgerType: string
{
    case Aggregation = 'Aggregation'; // 銀行口座の様に集計される台帳

    public function detailSettingModel(): string
    {
        return match ($this) {
            self::Aggregation => LedgerAggregationSetting::class,
        };
    }

    public function detailSettingResource(): string
    {
        return match ($this) {
            self::Aggregation => LedgerAggregationSettingResource::class,
        };
    }
}
