<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ServidorEfetivoController;
use App\Http\Controllers\ServidorTemporarioController;
use App\Http\Controllers\UnidadeController;
use App\Http\Controllers\LotacaoController;
use Illuminate\Http\Request;

Route::get('/', function () {
    return Inertia::render('auth/login');
})->name('home');

Route::post('/update-session-token', function (Request $request) {
    session(['api_token' => $request->token]);
    session(['token_expires_at' => $request->expires_at]);
    return response()->json(['success' => true]);
})->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('welcome', function () {
        return Inertia::render('welcome');
    })->name('welcome');
});

Route::resource('servidores/efetivo', ServidorEfetivoController::class, [
    'names' => 'servidores.efetivo',
    'parameters' => [
        'efetivo' => 'servidorEfetivo'
    ]
]);
Route::resource('servidores/temporario', ServidorTemporarioController::class, [
    'names' => 'servidores.temporario',
    'parameters' => [
        'temporario' => 'servidorTemporario'
    ]
]);
Route::resource('unidades', UnidadeController::class)
    ->middleware(['auth', 'verified'])
    ->names('unidades');
    
Route::resource('lotacao', LotacaoController::class)
    ->middleware(['auth', 'verified'])
    ->names('lotacao');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
