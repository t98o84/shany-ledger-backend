<?php

namespace App\Http\Middleware;

use App\Context\Context;
use Closure;
use Illuminate\Http\Request;

class AssignRequestId
{
    public function handle(Request $request, Closure $next)
    {
        if (!Context::hasRequestId()) {
            $requestId = (string) \Str::orderedUuid();
            Context::initRequestId($requestId);
        }


        return $next($request);
    }
}
