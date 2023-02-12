<?php

namespace App\Actions;

use App\Exceptions\ProblemDetails\ProblemDetailsException;

interface ErrorCode
{
    public function code(): string;

    public function title(): string;

    public function detail(): string|null;

    public function toProblemDetailException(): ProblemDetailsException;
}
