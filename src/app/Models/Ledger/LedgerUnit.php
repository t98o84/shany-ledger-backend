<?php

namespace App\Models\Ledger;

use App\ValueObjects\Ledger\LedgerUnitDisplayPosition;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class LedgerUnit extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'ledger_id';

    protected $guarded = ['id'];

    protected $casts = [
        'display_position' => LedgerUnitDisplayPosition::class,
    ];

    public function ledger(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Ledger::class);
    }

    public function validationRules(): array
    {
        return [
            'ledger_id' => ['bail', 'required', 'string', 'uuid', $this->getKey() ? Rule::exists(Ledger::make()->getTable()) : Rule::unique(Ledger::make()->getTable())],
            'symbol' => ['bail', 'required', 'string', 'max:255'],
            'display_position' => ['bail', 'required', 'string', Rule::enum(LedgerUnitDisplayPosition::class)],
            'description' => ['bail', 'nullable', 'string', 'max:255'],
        ];
    }
}
