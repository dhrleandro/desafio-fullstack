<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Str;

class RequestLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $requestId = (string) Str::uuid();
 
        Log::withContext([
            'request_id' => $requestId
        ]);
 
        Log::info("Request {$requestId}.", [
            'uri' => $request->getUri(),
            'method' => $request->getMethod(),
            'body' => $request->all(),
        ]);

        $response = $next($request);
 
        return $response;
    }
}
