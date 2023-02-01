<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Casts\Attribute;

class File extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'visibility' => FileVisibility::class,
        'size' => 'integer',
    ];

    public function fileable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function name(): Attribute
    {
        return Attribute::make(
            get: static fn($value, $attributes) => pathinfo($attributes['path'], PATHINFO_BASENAME)
        );
    }

    public function url(): Attribute
    {
        return Attribute::make(
            get: static fn($value, $attributes) => \Storage::disk($attributes['disk'])->url($attributes['path'])
        );
    }
}
