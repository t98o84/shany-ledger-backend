<?php

namespace App\Models\Ledger;

use App\Models\Traits\HasOperators;
use App\Models\Workspace\Workspace;
use App\ValueObjects\Ledger\LedgerPublicStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Ledger extends Model
{
    use HasFactory, SoftDeletes, HasUuids, HasOperators;

    protected $guarded = [
        'id', 'created_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = [
        'public_status' => LedgerPublicStatus::class,
    ];

    public function workspace(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Workspace::class);
    }

    public function unit(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(LedgerUnit::class);
    }

    public function publicStatusAnyoneSetting(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(LedgerPublicStatusAnyoneSetting::class);
    }

    public function validationRules(): array
    {
        return [
            'id' => ['bail', 'required', 'string', 'uuid', $this->getKey() ? Rule::exists($this->getTable()) : Rule::unique($this->getTable())],
            'workspace_id' => ['bail', 'required', 'string', 'uuid', Rule::exists(Workspace::make()->getTable())],
            'name' => ['bail', 'required', 'string', 'max:255'],
            'description' => ['bail', 'nullable', 'string', 'max:255'],
            'public_status' => ['bail', 'required', 'string', 'max:255', Rule::enum(LedgerPublicStatus::class)],
        ];
    }
}
