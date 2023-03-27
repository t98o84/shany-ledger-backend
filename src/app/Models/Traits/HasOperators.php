<?php

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait HasOperators
{
    protected static function bootHasOperators(): void
    {
        static::creating(static function (self $model) {
            if ($model->creatorColumn()) {
                $model->{$model->creatorColumn()} = $model->getAuthId();
            }
        });

        static::updating(static function (self $model) {
            if ($model->updaterColumn()) {
                $model->{$model->updaterColumn()} = $model->getAuthId();
            }
        });

        static::deleting(static function (self $model) {
            if ($model->deleterColumn()) {
                $model->{$model->deleterColumn()} = $model->getAuthId();
            }
        });
    }

    protected function getAuthId(): int|null|string
    {
        return \Auth::id();
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, $this->creatorColumn());
    }

    protected function creatorColumn(): ?string
    {
        return 'created_by';
    }

    public function updater(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, $this->updaterColumn());
    }

    protected function updaterColumn(): ?string
    {
        return 'updated_by';
    }

    public function deleter(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, $this->deleterColumn());
    }

    protected function deleterColumn(): ?string
    {
        return 'deleted_by';
    }
}
