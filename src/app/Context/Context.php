<?php

namespace App\Context;

class Context
{
    private static string|null $requestId = null;

    public function __construct()
    {
    }

    public static function initRequestId(string $requestId): void
    {
        if (static::$requestId) {
            throw new \LogicException('リクエストIDは一度設定したら変更できません。');
        }

        static::$requestId = $requestId;
    }

    public static function requestId(): string
    {
        if (is_null(static::$requestId)) {
            throw new \LogicException('リクエストIDが初期化される前に呼び出されました。');
        }
        return static::$requestId;
    }

    public static function hasRequestId(): bool
    {
        return !is_null(static::$requestId);
    }
}
