<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pessoa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Pessoas",
 *     description="Endpoints para gerenciamento de pessoas"
 * )
 */
class PessoaApiController extends Controller
{
        /**
     * @OA\Get(
     *     path="/pessoas",
     *     summary="Index pessoaapi",
     *     tags={"Pessoas"},
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
        $search = $request->input('search');
        
        $query = Pessoa::query();
        
        if ($search) {
            $query->where('pes_nome', 'like', "%{$search}%")
                  ->orWhere('pes_mae', 'like', "%{$search}%")
                  ->orWhere('pes_pai', 'like', "%{$search}%");
        }
        
        $pessoas = $query->paginate($perPage);
        
        return response()->json($pessoas);
    }

        /**
     * @OA\Get(
     *     path="/pessoas/{pessoa}",
     *     summary="Show pessoaapi",
     *     tags={"Pessoas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="pessoa",
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
public function show(Pessoa $pessoa)
    {
        $pessoa->load('enderecos.cidade', 'lotacoes.unidade');
        
        // Verificar se a pessoa é um servidor efetivo ou temporário
        $servidorEfetivo = $pessoa->servidorEfetivo;
        $servidorTemporario = $pessoa->servidorTemporario;
        
        return response()->json([
            'pessoa' => $pessoa,
            'servidor_efetivo' => $servidorEfetivo,
            'servidor_temporario' => $servidorTemporario
        ]);
    }

        /**
     * @OA\Put(
     *     path="/pessoas/{pessoa}",
     *     summary="Update pessoaapi",
     *     tags={"Pessoas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="pessoa",
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
public function update(Request $request, Pessoa $pessoa)
    {
        $validator = Validator::make($request->all(), [
            'pes_nome' => 'required|string|max:255',
            'pes_data_nascimento' => 'required|date',
            'pes_sexo' => 'required|string|max:1',
            'pes_mae' => 'required|string|max:255',
            'pes_pai' => 'required|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $pessoa->update($request->all());
        
        return response()->json([
            'message' => 'Pessoa atualizada com sucesso.',
            'pessoa' => $pessoa
        ]);
    }

        /**
     * @OA\Delete(
     *     path="/pessoas/{pessoa}",
     *     summary="Destroy pessoaapi",
     *     tags={"Pessoas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="pessoa",
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
public function destroy(Pessoa $pessoa)
    {
        try {
            // Verificar se a pessoa tem vínculos
            $temServidorEfetivo = $pessoa->servidorEfetivo()->exists();
            $temServidorTemporario = $pessoa->servidorTemporario()->exists();
            $temLotacoes = $pessoa->lotacoes()->exists();
            
            if ($temServidorEfetivo || $temServidorTemporario || $temLotacoes) {
                return response()->json([
                    'message' => 'Não é possível excluir esta pessoa pois ela possui vínculos como servidor ou lotações.'
                ], 409);
            }
            
            // Remover associações
            if ($pessoa->enderecos()->count() > 0) {
                $pessoa->enderecos()->detach();
            }
            
            if (method_exists($pessoa, 'fotos') && $pessoa->fotos()->count() > 0) {
                $pessoa->fotos()->delete();
            }
            
            $pessoa->delete();
            
            return response()->json([
                'message' => 'Pessoa excluída com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Não foi possível excluir a pessoa.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
