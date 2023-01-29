<?php

namespace App\Http\Middleware;

use App\Context\Context;
use Closure;
use Illuminate\Http\Request;

class AssignRequestId
{
    public function handle(Request $request, Closure $next)
    {
        $requestId = $request->cookie('request_id', (string) \Str::orderedUuid());

        if (!Context::hasRequestId()) {
            Context::initRequestId($requestId);
        }

        $response = $next($request);

        $response->cookie('request_id', (string) \Str::orderedUuid());

        return $response;
    }
}
