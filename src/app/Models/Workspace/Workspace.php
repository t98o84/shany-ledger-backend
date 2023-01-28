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

    public const BASE_FILE_PATH = '/workspace';

    public static function buildFilePath(string $id): string
    {
        return static::BASE_FILE_PATH . "/$id";
    }
}
