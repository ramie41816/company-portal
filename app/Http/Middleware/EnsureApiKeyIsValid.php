<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiKeyIsValid
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-KEY');

        if (!$apiKey || $apiKey !== config('services.portal.api_key')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized API Access: Invalid or missing API key.',
            ], 401);
        }

        return $next($request);
    }
}
