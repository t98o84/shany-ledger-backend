<?php

namespace App\Exceptions\ProblemDetails;

class UnauthorizedErrorException extends ProblemDetailsException
{

    public function defaultErrorCode(): string
    {
        return __('UnauthorizedError');
    }

    public function defaultTitle(): string
    {
        return __('error.unauthorized.title');
    }

    public function defaultStatus(): int
    {
        return 401;
    }

}
