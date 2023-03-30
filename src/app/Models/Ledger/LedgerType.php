<?php

namespace App\Models\Ledger;

enum LedgerType: string
{
    case Aggregation = 'Aggregation'; // 銀行口座の様に集計される台帳
}
