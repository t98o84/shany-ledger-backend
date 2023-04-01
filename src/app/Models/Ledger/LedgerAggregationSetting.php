<?php

namespace App\Models\Ledger;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class LedgerAggregationSetting extends Model implements LedgerDetailSetting
{
    use HasFactory;

    protected $guarded = ['ledger_id'];

    protected $attributes = [
        'max_input' => null,
        'min_input' => null,
        'max_output' => null,
        'min_output' => null,
        'max_total' => null,
        'min_total' => null,
        'fixed_point_number' => null,
    ];

    public function ledger(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Ledger::class);
    }

    public function validationRules(): array
    {
        return [
            'ledger_id' => ['bail', 'required', 'string', 'uuid', Rule::exists(Ledger::make()->getTable())],
            'max_input' => ['bail', 'nullable', 'numeric'],
            'min_input' => ['bail', 'nullable', 'numeric'],
            'max_output' => ['bail', 'nullable', 'numeric'],
            'min_output' => ['bail', 'nullable', 'numeric'],
            'max_total' => ['bail', 'nullable', 'numeric'],
            'min_total' => ['bail', 'nullable', 'numeric'],
            'fixed_point_number' => ['bail', 'nullable', 'numeric', 'min:0', 'max:5'],
        ];
    }
}
