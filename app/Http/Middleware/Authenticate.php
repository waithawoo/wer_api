<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authenticate extends Middleware
{
    // Customize to handle auth middleware for api reqs
    public function handle($request, Closure $next, ...$guards): Response
    {
        if ($request->hasHeader("Accept-Language")) {
            /**
             * If Accept-Language header found then set it to the default locale
             */
            app()->setLocale($request->header("Accept-Language"));
        }

        try {
            $this->authenticate($request, $guards);
        } catch (AuthenticationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'response' => [
                        'status' => __('messages.error'),
                        'message' => __('http.status_code_401'),
                    ],
                    'data' => [],
                ], 401);
            } else {
                return response()->json([
                    'response' => [
                        'status' => __('messages.error'),
                        'message' => __('http.status_code_404'),
                    ],
                    'data' => [],
                ], 404);
            }

        }

        return $next($request);
    }
}
