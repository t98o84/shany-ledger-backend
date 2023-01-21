<?php

namespace App\Exceptions\ProblemDetails;

class NotFoundErrorException extends ProblemDetailsException
{

    public function defaultErrorCode(): string
    {
        return 'NotFoundError';
    }

    public function defaultTitle(): string
    {
        return __('error.not_found.title');
    }

    public function defaultStatus(): int
    {
        return 404;
    }
}
