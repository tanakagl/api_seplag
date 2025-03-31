<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ServidorEfetivoController;
use App\Http\Controllers\ServidorTemporarioController;
use App\Http\Controllers\UnidadeController;
use App\Http\Controllers\LotacaoController;

Route::get('/', function () {
    return Inertia::render('auth/login');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('welcome', function () {
        return Inertia::render('welcome');
    })->name('welcome');
});

Route::resource('servidores/efetivo', ServidorEfetivoController::class, [
    'names' => 'servidores.efetivo'
]);
Route::resource('servidores/temporario', ServidorTemporarioController::class, [
    'names' => 'servidores.temporario'
]);
Route::resource('unidades', UnidadeController::class)
    ->middleware(['auth', 'verified'])
    ->names('unidades');
    
Route::resource('lotacao', LotacaoController::class)
    ->middleware(['auth', 'verified'])
    ->names('lotacao');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
