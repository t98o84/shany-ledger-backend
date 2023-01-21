<?php

namespace App\Exceptions\ProblemDetails;

class BadRequestsErrorException extends ProblemDetailsException
{
    public function defaultErrorCode(): string
    {
        return 'BadRequestsError';
    }

    public function defaultTitle(): string
    {
        return __('error.bad_requests.title');
    }

    public function defaultStatus(): int
    {
        return 400;
    }
}
