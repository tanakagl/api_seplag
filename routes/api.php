<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\EnderecoApiController;
use App\Http\Controllers\Api\ServidorEfetivoApiController;
use App\Http\Controllers\Api\ServidorTemporarioApiController;
use App\Http\Controllers\Api\UnidadeApiController;
use App\Http\Controllers\Api\LotacaoApiController;
use App\Http\Controllers\Api\PessoaApiController;
use App\Http\Controllers\Api\FotografiaApiController;
use Illuminate\Support\Facades\Route;

// Rotas públicas
Route::post('login', [AuthApiController::class, 'login']);

// Rotas protegidas
Route::middleware('auth:sanctum')->group(function () {
    // Rotas de autenticação
    Route::post('/me', [AuthApiController::class, 'me']);
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::post('/refresh-token', [AuthApiController::class, 'refreshToken']);

    // Rotas para leitura (todos os usuários)
    Route::middleware('ability:read')->group(function () {
        // Pessoas
        Route::get('/pessoas', [PessoaApiController::class, 'index']);
        Route::get('/pessoas/{pessoa}', [PessoaApiController::class, 'show']);

        // Endereços
        Route::get('/enderecos', [EnderecoApiController::class, 'index']);
        Route::get('/enderecos/{endereco}', [EnderecoApiController::class, 'show']);
        Route::get('/cidades', [EnderecoApiController::class, 'cidades']);

        // Servidores Efetivos
        Route::get('/servidores/efetivos', [ServidorEfetivoApiController::class, 'index']);
        Route::get('/servidores/efetivos/{servidorEfetivo}', [ServidorEfetivoApiController::class, 'show']);
        Route::get('/servidores-efetivos/unidade', [ServidorEfetivoApiController::class, 'getByUnidade']);
        Route::get('/servidores-efetivos/endereco-funcional', [ServidorEfetivoApiController::class, 'getEnderecoFuncional']);

        // Servidores Temporários
        Route::get('/servidores/temporarios', [ServidorTemporarioApiController::class, 'index']);
        Route::get('/servidores/temporarios/{servidorTemporario}', [ServidorTemporarioApiController::class, 'show']);

        // Unidades
        Route::get('/unidades', [UnidadeApiController::class, 'index']);
        Route::get('/unidades/{unidade}', [UnidadeApiController::class, 'show']);

        // Lotações
        Route::get('/lotacoes', [LotacaoApiController::class, 'index']);
        Route::get('/lotacoes/{lotacao}', [LotacaoApiController::class, 'show']);
        Route::get('/pessoas/{pessoa}/lotacoes', [LotacaoApiController::class, 'lotacoesPorPessoa']);
        Route::get('/unidades-lista', [LotacaoApiController::class, 'unidades']);
        
        // Fotografias
        Route::get('/fotografias/url-temporaria', [FotografiaApiController::class, 'getTemporaryUrl']);
        Route::get('/fotografias/pessoa', [FotografiaApiController::class, 'listByPessoa']);
    });

    // Rotas para criação (admin e manager)
    Route::middleware('ability:create')->group(function () {
        // Endereços
        Route::post('/enderecos', [EnderecoApiController::class, 'store']);
        Route::post('/pessoas/{pessoa}/enderecos', [EnderecoApiController::class, 'storeForPessoa']);
        Route::post('/pessoas/{pessoa}/enderecos/adicionar', [EnderecoApiController::class, 'addToPessoa']);

        // Servidores Efetivos
        Route::post('/servidores/efetivos', [ServidorEfetivoApiController::class, 'store']);

        // Servidores Temporários
        Route::post('/servidores/temporarios', [ServidorTemporarioApiController::class, 'store']);

        // Unidades
        Route::post('/unidades', [UnidadeApiController::class, 'store']);

        // Lotações
        Route::post('/lotacoes', [LotacaoApiController::class, 'store']);
        
        // Fotografias
        Route::post('/fotografias/upload', [FotografiaApiController::class, 'upload']);
    });

    // Rotas para atualização (admin e manager)
    Route::middleware('ability:update')->group(function () {
        // Endereços
        Route::put('/enderecos/{endereco}', [EnderecoApiController::class, 'update']);

        // Servidores Efetivos
        Route::put('/servidores/efetivos/{servidorEfetivo}', [ServidorEfetivoApiController::class, 'update']);
        
        // Servidores Temporários
        Route::put('/servidores/temporarios/{servidorTemporario}', [ServidorTemporarioApiController::class, 'update']);

        // Unidades
        Route::put('/unidades/{unidade}', [UnidadeApiController::class, 'update']);

        // Lotações
        Route::put('/lotacoes/{lotacao}', [LotacaoApiController::class, 'update']);

        Route::put('/pessoas/{pessoa}', [PessoaApiController::class, 'update']);
    });

    // Rotas para exclusão (apenas admin)
    Route::middleware('ability:delete')->group(function () {
        // Endereços
        Route::delete('/enderecos/{endereco}', [EnderecoApiController::class, 'destroy']);
        Route::delete('/pessoas/{pessoa}/enderecos/{endereco}', [EnderecoApiController::class, 'removeFromPessoa']);

        // Servidores Efetivos
        Route::delete('/servidores/efetivos/{servidorEfetivo}', [ServidorEfetivoApiController::class, 'destroy']);

        // Servidores Temporários
        Route::delete('/servidores/temporarios/{servidorTemporario}', [ServidorTemporarioApiController::class, 'destroy']);

        // Unidades
        Route::delete('/unidades/{unidade}', [UnidadeApiController::class, 'destroy']);

        // Lotações
        Route::delete('/lotacoes/{lotacao}', [LotacaoApiController::class, 'destroy']);

        Route::delete('/pessoas/{pessoa}', [PessoaApiController::class, 'destroy']);
        
        // Fotografias
        Route::delete('/fotografias/{id}', [FotografiaApiController::class, 'destroy']);
    });
});
