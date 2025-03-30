<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ServidorEfetivoController;
use App\Http\Controllers\ServidorTemporarioController;
use App\Http\Controllers\UnidadeController;
use App\Http\Controllers\LotacaoController;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

Route::resource('servidores/efetivo', ServidorEfetivoController::class, [
    'names' => 'servidores.efetivo'
]);
Route::resource('servidores/temporario', ServidorTemporarioController::class, [
    'names' => 'servidores.temporario'
]);
Route::resource('unidades', UnidadeController::class);
Route::resource('lotacao', LotacaoController::class);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
