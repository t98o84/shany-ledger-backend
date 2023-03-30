<?php

namespace App\Models\Ledger;

use App\Models\Traits\HasOperators;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class LedgerPublicStatusAnyoneSetting extends Model
{
    use HasFactory, HasUuids, HasOperators;

    protected $primaryKey = 'ledger_id';

    protected $guarded = ['ledger_id'];

    protected $attributes = [
        'allow_comments' => false,
        'allow_editing' => false,
        'allow_duplicate' => false,
        'expiration_started_at' => null,
        'expiration_ended_at' => null,
    ];

    protected $casts = [
        'allow_comments' => 'boolean',
        'allow_editing' => 'boolean',
        'allow_duplicate' => 'boolean',
        'expiration_started_at' => 'datetime',
        'expiration_ended_at' => 'datetime',
    ];

    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }

    public function validationRules(): array
    {
        return [
            'ledger_id' => ['bail', 'required', 'string', 'uuid', Rule::exists(Ledger::make()->getTable())],
            'url' => ['bail', 'required', 'string', 'max:255'],
            'allow_comments' => ['bail', 'required', 'boolean'],
            'allow_editing' => ['bail', 'required', 'boolean'],
            'allow_duplicate' => ['bail', 'required', 'boolean'],
            'expiration_started_at' => ['bail', 'nullable', 'date'],
            'expiration_ended_at' => ['bail', 'nullable', 'date'],
        ];
    }
}
