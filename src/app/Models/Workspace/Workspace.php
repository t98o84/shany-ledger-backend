<?php

namespace App\Models\Workspace;

use App\Models\Ledger\Ledger;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workspace extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    protected $with = [
        'icon',
    ];

    public const BASE_FILE_PATH = '/workspace';

    public function icon(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(WorkspaceIcon::class);
    }

    public function accounts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WorkspaceAccount::class);
    }

    public function findAccount(string $userId): ?WorkspaceAccount
    {
        return $this->accounts()->where('user_id', $userId)->first();
    }

    public function owner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function ledgers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ledger::class);
    }

    public function baseFilePath(): string
    {
        return static::BASE_FILE_PATH . "/$this->id";
    }


}
