<?php

namespace App\Models\Workspace;

use App\Models\Shared\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkspaceIcon extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public const CREATED_AT = null;

    public const UPDATED_AT = null;

    protected $guarded = [];

    protected $with = [
        'file',
    ];

    public function file(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function url(): string
    {
        return $this->file->url;
    }
}
