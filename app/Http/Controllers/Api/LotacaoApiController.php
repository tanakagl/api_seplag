<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lotacao;
use App\Models\Pessoa;
use App\Models\Unidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Lotações",
 *     description="Endpoints para gerenciamento de lotações"
 * )
 */
class LotacaoApiController extends Controller
{
        /**
     * @OA\Get(
     *     path="/lotacoes",
     *     summary="Index lotacaoapi",
     *     tags={"Lotações"},
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
        $lotacoes = Lotacao::with(['pessoa', 'unidade'])->paginate($perPage);
        
        return response()->json($lotacoes);
    }

        /**
     * @OA\Post(
     *     path="/lotacoes",
     *     summary="Store lotacaoapi",
     *     tags={"Lotações"},
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
            'pes_id' => 'required|exists:pessoa,pes_id',
            'unid_id' => 'required|exists:unidade,unid_id',
            'lot_data_admissao' => 'required|date',
            'lot_data_remocao' => 'nullable|date|after_or_equal:lot_data_admissao',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $lotacao = Lotacao::create($request->all());
        $lotacao->load(['pessoa', 'unidade']);
        
        return response()->json([
            'message' => 'Lotação criada com sucesso.',
            'lotacao' => $lotacao
        ], 201);
    }

        /**
     * @OA\Get(
     *     path="/lotacoes/{lotacao}",
     *     summary="Show lotacaoapi",
     *     tags={"Lotações"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="lotacao",
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
public function show(Lotacao $lotacao)
    {
        $lotacao->load(['pessoa', 'unidade']);
        
        return response()->json($lotacao);
    }

        /**
     * @OA\Put(
     *     path="/lotacoes/{lotacao}",
     *     summary="Update lotacaoapi",
     *     tags={"Lotações"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="lotacao",
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
public function update(Request $request, Lotacao $lotacao)
    {
        $validator = Validator::make($request->all(), [
            'pes_id' => 'required|exists:pessoa,pes_id',
            'unid_id' => 'required|exists:unidade,unid_id',
            'lot_data_admissao' => 'required|date',
            'lot_data_remocao' => 'nullable|date|after_or_equal:lot_data_admissao',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $lotacao->update($request->all());
        $lotacao->load(['pessoa', 'unidade']);
        
        return response()->json([
            'message' => 'Lotação atualizada com sucesso.',
            'lotacao' => $lotacao
        ]);
    }

        /**
     * @OA\Delete(
     *     path="/lotacoes/{lotacao}",
     *     summary="Destroy lotacaoapi",
     *     tags={"Lotações"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="lotacao",
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
public function destroy(Lotacao $lotacao)
    {
        try {
            $lotacao->delete();
            
            return response()->json([
                'message' => 'Lotação excluída com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Não foi possível excluir a lotação.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
        /**
     * @OA\Get(
     *     path="/pessoas/{pessoa}/lotacoes",
     *     summary="LotacoesPorPessoa lotacaoapi",
     *     tags={"Lotações"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="pessoa",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operação bem-sucedida"
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
public function lotacoesPorPessoa(Pessoa $pessoa)
    {
        $lotacoes = $pessoa->lotacoes()->with('unidade')->get();
        
        return response()->json($lotacoes);
    }
    
        /**
     * @OA\Get(
     *     path="/unidades-lista",
     *     summary="Unidades lotacaoapi",
     *     tags={"Lotações"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Operação bem-sucedida"
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
public function unidades()
    {
        $unidades = Unidade::orderBy('unid_nome')->get();
        
        return response()->json($unidades);
    }
}
