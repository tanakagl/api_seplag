<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Unidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Unidades",
 *     description="Endpoints para gerenciamento de unidades"
 * )
 */
class UnidadeApiController extends Controller
{
        /**
     * @OA\Get(
     *     path="/unidades",
     *     summary="Index unidadeapi",
     *     tags={"Unidades"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Operação bem-sucedida",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Recurso não encontrado"
     *     )
     * )
     */
public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $unidades = Unidade::paginate($perPage);
        
        return response()->json($unidades);
    }

        /**
     * @OA\Post(
     *     path="/unidades",
     *     summary="Store unidadeapi",
     *     tags={"Unidades"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operação bem-sucedida",
     *         @OA\JsonContent(
     *             type="object"
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     */
public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unid_nome' => 'required|string|max:255',
            'unid_sigla' => 'required|string|max:50',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $unidade = Unidade::create($request->all());
        
        return response()->json([
            'message' => 'Unidade criada com sucesso.',
            'unidade' => $unidade
        ], 201);
    }

        /**
     * @OA\Get(
     *     path="/unidades/{unidade}",
     *     summary="Show unidadeapi",
     *     tags={"Unidades"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="unidade",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operação bem-sucedida",
     *         @OA\JsonContent(
     *             type="object"
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Recurso não encontrado"
     *     )
     * )
     */
public function show(Unidade $unidade)
    {
        return response()->json($unidade);
    }

        /**
     * @OA\Put(
     *     path="/unidades/{unidade}",
     *     summary="Update unidadeapi",
     *     tags={"Unidades"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="unidade",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operação bem-sucedida",
     *         @OA\JsonContent(
     *             type="object"
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Recurso não encontrado"
     *     )
     * )
     */
public function update(Request $request, Unidade $unidade)
    {
        $validator = Validator::make($request->all(), [
            'unid_nome' => 'required|string|max:255',
            'unid_sigla' => 'required|string|max:50',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $unidade->update($request->all());
        
        return response()->json([
            'message' => 'Unidade atualizada com sucesso.',
            'unidade' => $unidade
        ]);
    }

        /**
     * @OA\Delete(
     *     path="/unidades/{unidade}",
     *     summary="Destroy unidadeapi",
     *     tags={"Unidades"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="unidade",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operação bem-sucedida",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Recurso não encontrado"
     *     )
     * )
     */
public function destroy(Unidade $unidade)
    {
        try {
            // Verificar se há lotações associadas a esta unidade
            $temLotacoes = \App\Models\Lotacao::where('unid_id', $unidade->unid_id)->exists();
            
            if ($temLotacoes) {
                return response()->json([
                    'message' => 'Não é possível excluir esta unidade pois existem lotações associadas a ela.'
                ], 409);
            }
            
            $unidade->delete();
            
            return response()->json([
                'message' => 'Unidade excluída com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Não foi possível excluir a unidade.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
