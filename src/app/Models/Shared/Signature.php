<?php

namespace App\Models\Shared;

use Carbon\Carbon;

class Signature
{
    public function __construct(
        public string $signature,
        public array  $parameters,
        public Carbon $expirationAt
    )
    {
    }

    public static function make(array $parameters, Carbon $expirationAt): self
    {
        return new self(
            self::generate($parameters, $expirationAt),
            $parameters,
            $expirationAt,
        );
    }

    public function valid(): bool
    {
        return !($this->expired() || $this->tampered());
    }

    public function tampered(): bool
    {
        $signature = self::generate($this->parameters, $this->expirationAt);

        return !hash_equals($signature, $this->signature);
    }

    public function expired(): bool
    {
        return Carbon::now()->gt($this->expirationAt);
    }

    public function equals($signature): bool
    {
        $signature = is_a(self::class, $signature) ? $signature->signature : $signature;

        return is_string($signature) && hash_equals($signature, $this->signature);
    }

    protected static function generate(array $parameters, Carbon $expirationAt): string
    {
        $parameters = array_values(\Arr::flatten($parameters));

        sort($parameters);

        return hash_hmac(
            'sha256',
            implode('|', $parameters) . $expirationAt->getTimestamp(),
            static::getKey()
        );
    }

    protected static function getKey(): string
    {
        return \Config::get('app.key');
    }
}
