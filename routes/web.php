<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ServidorEfetivoController;
use App\Http\Controllers\ServidorTemporarioController;
use App\Http\Controllers\UnidadeController;
use App\Http\Controllers\LotacaoController;
use Illuminate\Http\Request;

// Redirecionar a raiz para welcome
Route::get('/', function () {
    return redirect('/welcome');
})->name('home');

// Rota para atualizar o token na sessão
Route::post('/update-session-token', function (Request $request) {
    session(['api_token' => $request->token]);
    session(['token_expires_at' => $request->expires_at]);
    return response()->json(['success' => true]);
})->middleware('auth');

// Rotas protegidas por autenticação
Route::middleware('auth')->group(function () {
    // Página inicial
    Route::get('/welcome', function () {
        return Inertia::render('welcome');
    })->name('welcome');
    
    // Rotas de recursos
    Route::resource('servidores/efetivo', ServidorEfetivoController::class, [
        'names' => 'servidores.efetivo',
        'parameters' => ['efetivo' => 'servidorEfetivo']
    ]);
    
    Route::resource('servidores/temporario', ServidorTemporarioController::class, [
        'names' => 'servidores.temporario',
        'parameters' => ['temporario' => 'servidorTemporario']
    ]);
    
    Route::resource('unidade', UnidadeController::class)
        ->names('unidade');
    
    Route::resource('lotacao', LotacaoController::class)
        ->names('lotacao');
});

// Incluir outras rotas
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
