<?php

namespace App\Exceptions\ProblemDetails;

use Illuminate\Support\Carbon;

class TooManyRequestsErrorException extends ProblemDetailsException
{

    public function defaultErrorCode(): string
    {
        return 'TooManyRequestsError';
    }

    public function defaultTitle(): string
    {
        return match (true) {
            is_numeric($this->retryAfter) => __('error.too_many_requests.title_with_seconds', ['seconds' => $this->retryAfter]),
            $this->retryAfter instanceof Carbon => __('error.too_many_requests.title_with_datetime', ['datetime' => $this->retryAfter->format('Y-m-d H:i:s')]),
            default => __('error.too_many_requests.title')
        };
    }

    public function defaultStatus(): int
    {
        return 429;
    }

    /**
     * @param int|Carbon|null $retryAfter Carbonの場合は再試行が可能になる日時、intの場合は再リクエスト受付までの遅延秒数
     */
    public function __construct(public readonly int|Carbon|null $retryAfter = null)
    {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'retry_after' => is_a($this->retryAfter, Carbon::class) ? $this->retryAfter->toIso8601String() : $this->retryAfter,
        ];
    }

    protected function headers(): array
    {
        return is_null($this->retryAfter)
            ? parent::headers()
            : [
                ...parent::headers(),
                'Retry-After' => is_a($this->retryAfter, Carbon::class) ? $this->retryAfter->toIso8601String() : $this->retryAfter,
            ];
    }
}
