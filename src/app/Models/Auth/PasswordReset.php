<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'email';

    public const UPDATED_AT = null;

    protected $guarded = [];

    public static function hashToken(string $token): string
    {
        return \Hash::make($token);
    }

    public static function createToken(): string
    {
        return hash_hmac('sha256', \Str::random(40), static::getTokenKey());
    }

    protected static function getTokenKey(): string
    {
        return config('app.key') . '::PasswordReset';
    }

    public static function minutesToExpiration(): int
    {
        return config('auth.passwords.users.expire') ?? 60;
    }
}
