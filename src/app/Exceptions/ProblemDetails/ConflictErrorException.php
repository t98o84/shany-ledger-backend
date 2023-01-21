<?php

namespace App\Exceptions\ProblemDetails;

class ConflictErrorException extends ProblemDetailsException
{
    public function defaultErrorCode(): string
    {
        return 'ConflictError';
    }

    public function defaultTitle(): string
    {
        return __('error.conflict.title');
    }

    public function defaultStatus(): int
    {
        return 409;
    }

}
