<?php

namespace App\Http\Middleware;

use App\Context\Context;
use Closure;
use Illuminate\Http\Request;

class RecordRequestLogs
{
    private static array $startHrtime;

    private static array $endHrtime;

    public function handle(Request $request, Closure $next)
    {
        static::$startHrtime = hrtime();

        return $next($request);
    }

    public function terminate(Request $request, $response): void
    {
        static::$endHrtime = hrtime();

        $this->record($request, $response);
    }

    private function record(Request $request, $response): void
    {
        // TODO: 項目を精査
        $context = [
            'id' => (string)\Str::orderedUuid(),
            'user' => [
                'id' => $request->user()?->id,
            ],
            'request' => [
                'id' => Context::requestId(),
                'method' => $request->getMethod(),
                'ip' => $request->ip(),
                'ips' => $request->ips(),
                'referer' => $request->header('referer'),
                'uri' => $request->getUri(),
                'user_agent' => $request->userAgent(),
                'locale' => $request->getLocale(),
                'timestamp' => static::$startHrtime[0],
                'timestamp_ns' => static::$startHrtime[1],
            ],
            'response' => [
                'status' => is_object($response) && method_exists($response, 'status') ? $response->status() : null,
                'timestamp' => static::$endHrtime[0],
                'timestamp_ns' => static::$endHrtime[1],
            ],
        ];

        // TODO: 専用のチャンネルを作成する
        \Log::info("Request log: ", $context);
    }


}
