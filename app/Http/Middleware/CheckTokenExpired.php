<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckTokenExpired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('api_token') || !Session::has('token_expires_at')) {
            return redirect()->route('login')->with('error', 'Sua sessão expirou. Por favor, faça login novamente.');
        }

        $expiresAt = strtotime(Session::get('token_expires_at'));
        $now = now()->timestamp; // Timestamp atual

        if ($now >= $expiresAt) {
            return redirect()->route('login')->with('error', 'Sua sessão expirou. Por favor, faça login novamente.');
        }

        \Inertia\Inertia::share([
            'auth' => [
                'token_expires_at' => $expiresAt,
                'token_expires_in_seconds' => $expiresAt - $now 
            ]
        ]);

        return $next($request);
    }
}
