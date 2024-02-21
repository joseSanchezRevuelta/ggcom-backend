<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithSanctum
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if (!$request->expectsJson()) {
        //     throw new AuthenticationException(
        //         'Unauthenticated.', ['sanctum'], route('login')
        //     );
        // }

        $token = $request->bearerToken();

        if ($token && auth()->guard('sanctum')->check($token)) {
            // El token de autenticación es válido
            return $next($request);
        } else {
            // El token de autenticación no es válido
            return response()->json(['error' => 'Token de autenticación inválido'], 401);
        }

        return $next($request);
    }
}
