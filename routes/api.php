<?php

use App\Http\Controllers\Api\AuthApiController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthApiController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/me', [AuthApiController::class, 'me']);
        Route::post('/logout', [AuthApiController::class, 'logout']);

        Route::post('/refresh-token', [AuthApiController::class, 'refreshToken']);

        Route::middleware('ability:read')->group(function () {
            Route::get('/data', function() {
                return response()->json([
                    'message' => 'Dados de leitura',
                ]);
            });
            Route::middleware('ability:create')->group(function () {
                Route::post('/data', function() {
                    return response()->json([
                        'message' => 'Dados criados',
                    ]);
                });
            });
            Route::middleware('ability:update')->group(function () {
                Route::put('/data/{id}', function($id) {
                  return response()->json([
                    'message' => 'Dados atualizados',
                  ]);
                });
            });
            Route::middleware('ability:delete')->group(function () {
                Route::delete('/data/{id}', function($id) {
                    return response()->json([
                        'message' => 'Dados deletados',
                    ]);
                });
            });
        });
    });