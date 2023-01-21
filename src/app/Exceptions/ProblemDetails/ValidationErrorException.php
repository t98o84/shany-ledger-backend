<?php

namespace App\Exceptions\ProblemDetails;

use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;

class ValidationErrorException extends BadRequestsErrorException
{

    public function defaultErrorCode(): string
    {
        return 'ValidationError';
    }

    public function defaultTitle(): string
    {
        return __('error.validation.title');
    }

    /**
     * @param array<ValidationErrorItem> $errors
     */
    public function __construct(
        public readonly array $errors,
    )
    {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'errors' => array_map(static fn($error) => $error->toArray(), $this->errors),
        ];
    }

    public static function makeFromValidationException(ValidationException $exception): ValidationErrorException
    {
        return new ValidationErrorException(array_map(
            static fn(array $messages, string $field) => new ValidationErrorItem($field, $messages[array_key_first($messages)]),
            array_values($exception->errors()),
            array_keys($exception->errors())
        ));
    }

}
