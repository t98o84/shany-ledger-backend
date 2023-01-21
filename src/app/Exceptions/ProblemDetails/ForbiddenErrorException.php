<?php

namespace App\Exceptions\ProblemDetails;

class ForbiddenErrorException extends ProblemDetailsException
{
    public function defaultErrorCode(): string
    {
        return 'ForbiddenError';
    }

    public function defaultTitle(): string
    {
        return __('error.forbidden.title');
    }

    public function defaultStatus(): int
    {
        return 403;
    }

}
