<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Shared\File;
use App\Models\Workspace\Workspace;
use App\Models\Workspace\WorkspaceAccount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    public const BASE_FILE_PATH = '/user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $with = [
        'avatar',
    ];

    public function avatar(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserAvatar::class);
    }

    public function workspaceAccounts()
    {
        return $this->hasMany(WorkspaceAccount::class);
    }

    public function workspaces()
    {
        return $this->belongsToMany(Workspace::class, WorkspaceAccount::class);
    }

    public static function hashPassword(string $password): string
    {
        return Hash::make($password);
    }

    public function equalPassword(string $rawPassword): bool
    {
        return Hash::check($rawPassword, $this->password);
    }

    public function setPassword($rawPassword): static
    {
        $this->password = static::hashPassword($rawPassword);
        return $this;
    }

    public function baseFilePath(): string
    {
        return static::BASE_FILE_PATH . "/$this->id";
    }
}
