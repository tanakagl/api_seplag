<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ServidorEfetivoController;
use App\Http\Controllers\ServidorTemporarioController;
use App\Http\Controllers\EnderecoController;
use App\Http\Controllers\UnidadeController;
use App\Http\Controllers\LotacaoController;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// Redirecionar a raiz para welcome
Route::get('/', function () {
    return redirect('/welcome');
})->name('home');

// Rotas de autenticação que não precisam de verificação de token
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// Rota para atualizar o token na sessão
Route::post('/update-session-token', function (Request $request) {
    session(['api_token' => $request->token]);
    session(['token_expires_at' => $request->expires_at]);
    return response()->json(['success' => true]);
})->middleware('auth');

// Rotas protegidas por autenticação e verificação de token
Route::middleware(['auth', 'check.token'])->group(function () {
    // Página inicial
    Route::get('/welcome', function () {
        return Inertia::render('welcome');
    })->name('welcome');

    // Recursos de servidores efetivos
    Route::resource('servidores/efetivo', ServidorEfetivoController::class, [
        'names' => 'servidores.efetivo',
        'parameters' => ['efetivo' => 'servidorEfetivo']
    ]);

    // Recursos de servidores temporários
    Route::resource('servidores/temporario', ServidorTemporarioController::class, [
        'names' => 'servidores.temporario',
        'parameters' => ['temporario' => 'servidorTemporario']
    ]);

    // Recursos de unidades
    Route::resource('unidade', UnidadeController::class)
        ->names('unidade');

    // Recursos de lotações
    Route::resource('lotacao', LotacaoController::class)
        ->names('lotacao');

    // Recursos de endereços
    Route::resource('endereco', EnderecoController::class);

    // Rotas para associar endereços a pessoas
    Route::get('pessoa/{pessoa}/endereco/create', [EnderecoController::class, 'createForPessoa'])
        ->name('pessoa.endereco.create');
    
    Route::post('pessoa/{pessoa}/endereco', [EnderecoController::class, 'storeForPessoa'])
        ->name('pessoa.endereco.store');
    
    Route::post('pessoa/{pessoa}/endereco/add', [EnderecoController::class, 'addToPessoa'])
        ->name('pessoa.endereco.add');
    
    Route::delete('pessoa/{pessoa}/endereco/{endereco}', [EnderecoController::class, 'removeFromPessoa'])
        ->name('pessoa.endereco.remove');
});

// Incluir outras rotas
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
