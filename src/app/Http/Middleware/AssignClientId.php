<?php

namespace App\Http\Middleware;

use App\Context\Context;
use Closure;
use Illuminate\Http\Request;

class AssignClientId
{
    public function handle(Request $request, Closure $next)
    {
        $clientId = $request->cookie('client_id', (string) \Str::orderedUuid());

        $request->cookies->set('client_id', $clientId);

        if (!Context::hasClientId()) {
            Context::initClientId($clientId);
        }

        return $next($request);
    }
}
