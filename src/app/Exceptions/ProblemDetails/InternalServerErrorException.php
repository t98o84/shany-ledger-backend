<?php

namespace App\Exceptions\ProblemDetails;

class InternalServerErrorException extends ProblemDetailsException
{
    public function defaultErrorCode(): string
    {
        return 'InternalServerError';
    }

    public function defaultTitle(): string
    {
        return __('error.internal_server_error.title');
    }

    public function defaultStatus(): int
    {
        return 500;
    }

    public function report(): bool
    {
        return true;
    }
}
