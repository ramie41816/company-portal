<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (! auth()->check() || ! auth()->user()->hasPermission($permission)) {
            return response()
                ->view('errors.403', [
                    'message' => 'You do not have permission to access that page.',
                ], 403)
                ->header('Refresh', '5;url=' . route('home'));
        }

        return $next($request);
    }
}
