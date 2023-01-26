<?php

namespace App\Jobs;

class ExponentialBackoff
{
    public static function generateDelayRetryList($tries = 5, int $baseSeconds = 1, int $exponent = 2): array
    {
        return array_map(static fn(int $attempts) => static::calculateDelayRetry($attempts, $baseSeconds, $exponent), range(1, $tries));
    }

    public static function calculateDelayRetry(int $attempts = 1, int $baseSeconds = 1, int $exponent = 2): int
    {
        return  $baseSeconds * ($exponent ** ($attempts - 1));
    }
}
