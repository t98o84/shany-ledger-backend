<?php

namespace App\Models\Workspace;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;

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

    public static function buildFilePath(string $id): string
    {
        return static::BASE_FILE_PATH . "/$id";
    }
}
