<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
        
        try {
            // Gerar token API para o usuário logado
            $user = Auth::user();
            $abilities = $this->getAbilitiesForRole($user->role);
            $token = $user->createToken('api-token', $abilities)->plainTextToken;
            
            // Armazenar o token e sua expiração na sessão
            session(['api_token' => $token]);
            session(['token_expires_at' => now()->addMinutes(5)->toDateTimeString()]);
            session(['token_abilities' => $abilities]);
            
            \Log::info('Login bem-sucedido, redirecionando para welcome');
            
            // Use redirecionamento direto em vez de intended
            return redirect('/welcome');
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar token: ' . $e->getMessage());
            return redirect('/welcome');
        }
    }
    

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Remover o token API se existir
        if ($request->user()) {
            $request->user()->tokens()->delete();
        }
        
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('login');
    }
    
    /**
     * Definir as habilidades com base na role do usuario
     */
    private function getAbilitiesForRole(string $role)
    {
        return match ($role) {
            'admin' => ['*'],
            'manager' => ['read', 'create', 'update'],
            default => ['read'],
        };
    }
}
