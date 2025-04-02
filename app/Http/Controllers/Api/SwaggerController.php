<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *     title="API SEPLAG",
 *     version="1.0.0",
 *     description="API para gerenciamento de servidores públicos, incluindo servidores efetivos, temporários, lotações, endereços e fotografias.",
 *     @OA\Contact(
 *         email="matheorb@hotmail.com",
 *         name="Matheo R Bonucia"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="/api",
 *     description="Servidor da API"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * 
 * @OA\Tag(name="Autenticação", description="Endpoints para autenticação e gerenciamento de tokens")
 * @OA\Tag(name="Servidores Efetivos", description="Gerenciamento de servidores efetivos")
 * @OA\Tag(name="Servidores Temporários", description="Gerenciamento de servidores temporários")
 * @OA\Tag(name="Unidades", description="Gerenciamento de unidades administrativas")
 * @OA\Tag(name="Lotações", description="Gerenciamento de lotações de servidores")
 * @OA\Tag(name="Endereços", description="Gerenciamento de endereços")
 * @OA\Tag(name="Fotografias", description="Upload e gerenciamento de fotografias")
 * @OA\Tag(name="Pessoas", description="Gerenciamento de dados pessoais")
 */
class SwaggerController extends Controller
{
    // Este controller serve apenas para documentação
}