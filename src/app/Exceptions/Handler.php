<?php

namespace App\Exceptions;

use App\Exceptions\ProblemDetails\ForbiddenErrorException;
use App\Exceptions\ProblemDetails\InternalServerErrorException;
use App\Exceptions\ProblemDetails\NotFoundErrorException;
use App\Exceptions\ProblemDetails\UnauthorizedErrorException;
use App\Exceptions\ProblemDetails\ValidationErrorException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (Throwable $e, $request) {
            if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException || $e instanceof MethodNotAllowedHttpException) {
                return (new NotFoundErrorException)->render();
            }

            if ($e instanceof AuthenticationException) {
                return (new UnauthorizedErrorException)->render();
            }

            if ($e instanceof AuthorizationException) {
                return (new ForbiddenErrorException)->render();
            }

            if ($e instanceof ValidationException) {
                return ValidationErrorException::makeFromValidationException($e)->render();
            }

            if ($e instanceof InternalErrorException) {
                return (new InternalServerErrorException)->render();
            }
        });
    }
}
