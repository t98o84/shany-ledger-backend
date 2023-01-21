<?php

namespace App\Exceptions\ProblemDetails;

readonly class ValidationErrorItem
{

    public function __construct(public string $field, public string $title, public string|null $detail = null)
    {
    }

    public function toArray(): array
    {
        return [
            'field' => $this->field,
            'title' => $this->title,
            'detail' => $this->detail,
        ];
    }
}
