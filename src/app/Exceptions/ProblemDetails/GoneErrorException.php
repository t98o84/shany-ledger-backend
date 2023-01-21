<?php

namespace App\Exceptions\ProblemDetails;

class GoneErrorException extends ProblemDetailsException
{
    public function defaultErrorCode(): string
    {
        return 'GoneError';
    }

    public function defaultTitle(): string
    {
        return __('error.gone.title');
    }

    public function defaultStatus(): int
    {
        return 409;
    }
}
