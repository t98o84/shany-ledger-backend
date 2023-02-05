<?php

namespace App\Http\ErrorCodeHandlers\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Exceptions\ProblemDetails\BadRequestsErrorException;
use App\Exceptions\ProblemDetails\ForbiddenErrorException;
use App\Exceptions\ProblemDetails\NotFoundErrorException;
use App\Exceptions\ProblemDetails\ProblemDetailsException;

class AuthErrorCodeHandler
{
    /**
     * @param AuthErrorCode $error
     * @return void
     * @throws ProblemDetailsException
     */
    public function handle(AuthErrorCode $error): void
    {
        match ($error) {
            AuthErrorCode::UserNotExists => throw new NotFoundErrorException(),
            AuthErrorCode::Forbidden => throw new ForbiddenErrorException(),
            default => throw new BadRequestsErrorException($error->value, $error->message())
        };
    }
}
