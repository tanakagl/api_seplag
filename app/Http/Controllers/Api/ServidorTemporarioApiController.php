<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pessoa;
use App\Models\ServidorTemporario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Servidores Temporários",
 *     description="Endpoints para gerenciamento de servidores temporários"
 * )
 */
class ServidorTemporarioApiController extends Controller
{
        /**
     * @OA\Get(
     *     path="/servidores/temporarios",
     *     summary="Index servidortemporarioapi",
     *     tags={"Servidores Temporários"},
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
        $servidores = ServidorTemporario::with('pessoa')->paginate($perPage);
        
        return response()->json($servidores);
    }

        /**
     * @OA\Post(
     *     path="/servidores/temporarios",
     *     summary="Store servidortemporarioapi",
     *     tags={"Servidores Temporários"},
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
            'pes_nome' => 'required|string|max:255',
            'pes_data_nascimento' => 'required|date',
            'pes_sexo' => 'required|string|max:1',
            'pes_mae' => 'required|string|max:255',
            'pes_pai' => 'required|string|max:255',
            'st_data_admissao' => 'required|date',
            'st_data_demissao' => 'nullable|date|after_or_equal:st_data_admissao',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $servidor = null;
        
        DB::transaction(function () use ($request, &$servidor) {
            $pessoa = Pessoa::create([
                'pes_nome' => $request->pes_nome,
                'pes_data_nascimento' => $request->pes_data_nascimento,
                'pes_sexo' => $request->pes_sexo,
                'pes_mae' => $request->pes_mae,
                'pes_pai' => $request->pes_pai,
            ]);
            
            $servidor = ServidorTemporario::create([
                'pes_id' => $pessoa->pes_id,
                'st_data_admissao' => $request->st_data_admissao,
                'st_data_demissao' => $request->st_data_demissao,
            ]);
            
            $servidor->load('pessoa');
        });
        
        return response()->json([
            'message' => 'Servidor temporário cadastrado com sucesso.',
            'servidor' => $servidor
        ], 201);
    }

        /**
     * @OA\Get(
     *     path="/servidores/temporarios/{servidorTemporario}",
     *     summary="Show servidortemporarioapi",
     *     tags={"Servidores Temporários"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="servidorTemporario",
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
public function show(ServidorTemporario $servidorTemporario)
    {
        $servidorTemporario->load('pessoa', 'pessoa.enderecos', 'pessoa.lotacoes.unidade');
        
        return response()->json($servidorTemporario);
    }

        /**
     * @OA\Put(
     *     path="/servidores/temporarios/{servidorTemporario}",
     *     summary="Update servidortemporarioapi",
     *     tags={"Servidores Temporários"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="servidorTemporario",
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
public function update(Request $request, ServidorTemporario $servidorTemporario)
    {
        $validator = Validator::make($request->all(), [
            'pes_nome' => 'required|string|max:255',
            'pes_data_nascimento' => 'required|date',
            'pes_sexo' => 'required|string|max:1',
            'pes_mae' => 'required|string|max:255',
            'pes_pai' => 'required|string|max:255',
            'st_data_admissao' => 'required|date',
            'st_data_demissao' => 'nullable|date|after_or_equal:st_data_admissao',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::transaction(function () use ($request, $servidorTemporario) {
            $servidorTemporario->pessoa->update([
                'pes_nome' => $request->pes_nome,
                'pes_data_nascimento' => $request->pes_data_nascimento,
                'pes_sexo' => $request->pes_sexo,
                'pes_mae' => $request->pes_mae,
                'pes_pai' => $request->pes_pai,
            ]);

            $servidorTemporario->update([
                'st_data_admissao' => $request->st_data_admissao,
                'st_data_demissao' => $request->st_data_demissao,
            ]);
        });
        
        $servidorTemporario->load('pessoa');
        
        return response()->json([
            'message' => 'Servidor temporário atualizado com sucesso.',
            'servidor' => $servidorTemporario
        ]);
    }

        /**
     * @OA\Delete(
     *     path="/servidores/temporarios/{servidorTemporario}",
     *     summary="Destroy servidortemporarioapi",
     *     tags={"Servidores Temporários"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="servidorTemporario",
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
public function destroy(ServidorTemporario $servidorTemporario)
    {
        try {
            DB::transaction(function () use ($servidorTemporario) {
                $pessoa = $servidorTemporario->pessoa;
                
                $servidorTemporario->delete();
                
                if ($pessoa) {
                    $temOutrosVinculos = (
                        \App\Models\ServidorEfetivo::where('pes_id', $pessoa->pes_id)->exists() ||
                        \App\Models\Lotacao::where('pes_id', $pessoa->pes_id)->exists()
                    );
                    
                    if (!$temOutrosVinculos) {
                        if (method_exists($pessoa, 'enderecos') && $pessoa->enderecos()->count() > 0) {
                            $pessoa->enderecos()->detach();
                        }
                        
                        if (method_exists($pessoa, 'fotos') && $pessoa->fotos()->count() > 0) {
                            $pessoa->fotos()->delete();
                        }
                        
                        $pessoa->delete();
                    }
                }
            });
            
            return response()->json([
                'message' => 'Servidor temporário excluído com sucesso.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir servidor: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            return response()->json([
                'message' => 'Não foi possível excluir o servidor.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

