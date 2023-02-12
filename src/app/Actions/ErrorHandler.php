<?php

namespace App\Actions;

interface ErrorHandler
{
    public function handle(ErrorCode $code): void;
}
