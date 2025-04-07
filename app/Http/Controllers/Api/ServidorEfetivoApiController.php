<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServidorEfetivo;
use App\Models\Pessoa;
use App\Models\Unidade;
use App\Models\Lotacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

/**
 * @OA\Tag(
 *     name="Servidores Efetivos",
 *     description="Endpoints para gerenciamento de servidores efetivos"
 * )
 */
class ServidorEfetivoApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/servidores/efetivos",
     *     summary="Listar todos os servidores efetivos",
     *     tags={"Servidores Efetivos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número da página",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de servidores efetivos",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="last_page", type="integer"),
     *             @OA\Property(property="per_page", type="integer"),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function index()
    {
        $servidores = ServidorEfetivo::with('pessoa')->paginate(10);
        return response()->json($servidores);
    }

    /**
     * @OA\Get(
     *     path="/api/servidores/efetivos/{servidorEfetivo}",
     *     summary="Obter detalhes de um servidor efetivo",
     *     tags={"Servidores Efetivos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="servidorEfetivo",
     *         in="path",
     *         description="ID do servidor efetivo",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do servidor efetivo",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Servidor não encontrado"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function show(ServidorEfetivo $servidorEfetivo)
    {
        $servidorEfetivo->load('pessoa', 'pessoa.enderecos', 'pessoa.lotacoes.unidade');
        return response()->json($servidorEfetivo);
    }

    /**
     * @OA\Post(
     *     path="/api/servidores/efetivos",
     *     summary="Criar um novo servidor efetivo",
     *     tags={"Servidores Efetivos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"pes_nome","pes_data_nascimento","pes_sexo","pes_mae","pes_pai","se_matricula"},
     *             @OA\Property(property="pes_nome", type="string"),
     *             @OA\Property(property="pes_data_nascimento", type="string", format="date"),
     *             @OA\Property(property="pes_sexo", type="string", enum={"M", "F"}),
     *             @OA\Property(property="pes_mae", type="string"),
     *             @OA\Property(property="pes_pai", type="string"),
     *             @OA\Property(property="se_matricula", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Servidor criado com sucesso",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'pes_nome' => 'required|string|max:255',
            'pes_data_nascimento' => 'required|date',
            'pes_sexo' => 'required|string|max:1',
            'pes_mae' => 'required|string|max:255',
            'pes_pai' => 'required|string|max:255',
            'se_matricula' => 'required|string|max:20|unique:servidor_efetivo',
        ]);

        $pessoa = null;
        $servidor = null;

        DB::transaction(function () use ($request, &$pessoa, &$servidor) {
            $pessoa = Pessoa::create([
                'pes_nome' => $request->pes_nome,
                'pes_data_nascimento' => $request->pes_data_nascimento,
                'pes_sexo' => $request->pes_sexo,
                'pes_mae' => $request->pes_mae,
                'pes_pai' => $request->pes_pai,
            ]);
            
            $servidor = ServidorEfetivo::create([
                'pes_id' => $pessoa->pes_id,
                'se_matricula' => $request->se_matricula,
            ]);
        });
        
        $servidor->load('pessoa');
        return response()->json($servidor, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/servidores/efetivos/{servidorEfetivo}",
     *     summary="Atualizar um servidor efetivo",
     *     tags={"Servidores Efetivos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="servidorEfetivo",
     *         in="path",
     *         description="ID do servidor efetivo",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"pes_nome","pes_data_nascimento","pes_sexo","pes_mae","pes_pai","se_matricula"},
     *             @OA\Property(property="pes_nome", type="string"),
     *             @OA\Property(property="pes_data_nascimento", type="string", format="date"),
     *             @OA\Property(property="pes_sexo", type="string", enum={"M", "F"}),
     *             @OA\Property(property="pes_mae", type="string"),
     *             @OA\Property(property="pes_pai", type="string"),
     *             @OA\Property(property="se_matricula", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Servidor atualizado com sucesso",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Servidor não encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function update(Request $request, ServidorEfetivo $servidorEfetivo)
    {
        $request->validate([
            'pes_nome' => 'required|string|max:255',
            'pes_data_nascimento' => 'required|date',
            'pes_sexo' => 'required|string|max:1',
            'pes_mae' => 'required|string|max:255',
            'pes_pai' => 'required|string|max:255',
            'se_matricula' => [
                'required', 'string', 'max:20',
                Rule::unique('servidor_efetivo', 'se_matricula')->ignore($servidorEfetivo->pes_id, 'pes_id')
            ],
        ]);

        DB::transaction(function () use ($request, $servidorEfetivo) {
            $servidorEfetivo->pessoa->update([
                'pes_nome' => $request->pes_nome,
                'pes_data_nascimento' => $request->pes_data_nascimento,
                'pes_sexo' => $request->pes_sexo,
                'pes_mae' => $request->pes_mae,
                'pes_pai' => $request->pes_pai,
            ]);

            $servidorEfetivo->update([
                'se_matricula' => $request->se_matricula,
            ]);
        });
        
        $servidorEfetivo->load('pessoa');
        return response()->json($servidorEfetivo);
    }

    /**
     * @OA\Delete(
     *     path="/api/servidores/efetivos/{servidorEfetivo}",
     *     summary="Excluir um servidor efetivo",
     *     tags={"Servidores Efetivos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="servidorEfetivo",
     *         in="path",
     *         description="ID do servidor efetivo",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Servidor excluído com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Servidor não encontrado"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function destroy(ServidorEfetivo $servidorEfetivo)
    {
        try {
            DB::transaction(function () use ($servidorEfetivo) {
                $pessoa = $servidorEfetivo->pessoa;
                
                $servidorEfetivo->delete();
                
                if ($pessoa) {
                    $temOutrosVinculos = (
                        \App\Models\ServidorTemporario::where('pes_id', $pessoa->pes_id)->exists() ||
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
            
            return response()->json(['message' => 'Servidor efetivo excluído com sucesso.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao excluir servidor: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/servidores-efetivos/unidade",
     *     summary="Consultar servidores efetivos por unidade",
     *     tags={"Servidores Efetivos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="unid_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de servidores efetivos da unidade",
     *         @OA\JsonContent(
     *             @OA\Property(property="servidores", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     */
    public function getByUnidade(Request $request)
    {
        $request->validate([
            'unid_id' => 'required|exists:unidade,unid_id',
        ]);

        $unidadeId = $request->unid_id;

        // Buscar servidores efetivos lotados na unidade especificada
        $servidores = ServidorEfetivo::with(['pessoa', 'pessoa.fotos'])
            ->whereHas('pessoa.lotacoes', function ($query) use ($unidadeId) {
                $query->where('unid_id', $unidadeId)
                    ->where(function ($q) {
                        $q->whereNull('lot_data_remocao')
                          ->orWhere('lot_data_remocao', '>=', now());
                    });
            })->get();

        // Formatar os dados para retornar apenas os campos necessários
        $result = $servidores->map(function ($servidor) {
            // Calcular idade
            $dataNascimento = Carbon::parse($servidor->pessoa->pes_data_nascimento);
                       $idade = $dataNascimento->age;

            // Buscar unidade de lotação atual
            $lotacaoAtual = $servidor->pessoa->lotacoes()
                ->with('unidade')
                ->where(function ($query) {
                    $query->whereNull('lot_data_remocao')
                          ->orWhere('lot_data_remocao', '>=', now());
                })->first();

            // Gerar links temporários para as fotos
            $fotos = [];
            if ($servidor->pessoa->fotos && count($servidor->pessoa->fotos) > 0) {
                foreach ($servidor->pessoa->fotos as $foto) {
                    // Gerar link temporário com expiração de 5 minutos
                    $fotos[] = [
                        'id' => $foto->fot_id,
                        'url' => $this->generateTemporaryUrl($foto->fot_caminho),
                    ];
                }
            }

            return [
                'nome' => $servidor->pessoa->pes_nome,
                'idade' => $idade,
                'unidade_lotacao' => $lotacaoAtual ? [
                    'id' => $lotacaoAtual->unidade->unid_id,
                    'nome' => $lotacaoAtual->unidade->unid_nome,
                    'sigla' => $lotacaoAtual->unidade->unid_sigla,
                ] : null,
                'fotografias' => $fotos,
            ];
        });

        return response()->json([
            'servidores' => $result,
            'total' => $result->count(),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/servidores-efetivos/endereco-funcional",
     *     summary="Consultar endereço funcional por nome parcial do servidor",
     *     tags={"Servidores Efetivos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="nome",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Endereços funcionais dos servidores",
     *         @OA\JsonContent(
     *             @OA\Property(property="resultados", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     */
    public function getEnderecoFuncional(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|min:3',
        ]);

        $nomeParcial = $request->nome;

        // Buscar servidores efetivos pelo nome parcial
        $servidores = ServidorEfetivo::whereHas('pessoa', function ($query) use ($nomeParcial) {
            $query->where('pes_nome', 'like', "%{$nomeParcial}%");
        })->with(['pessoa.lotacoes.unidade.endereco'])->get();

        // Formatar os dados para retornar apenas os endereços funcionais
        $result = $servidores->map(function ($servidor) {
            // Buscar lotação atual
            $lotacaoAtual = $servidor->pessoa->lotacoes()
                ->with(['unidade.endereco'])
                ->where(function ($query) {
                    $query->whereNull('lot_data_remocao')
                          ->orWhere('lot_data_remocao', '>=', now());
                })->first();

            if (!$lotacaoAtual || !$lotacaoAtual->unidade || !$lotacaoAtual->unidade->endereco) {
                return [
                    'servidor' => $servidor->pessoa->pes_nome,
                    'endereco_funcional' => null,
                ];
            }

            $endereco = $lotacaoAtual->unidade->endereco;
            return [
                'servidor' => $servidor->pessoa->pes_nome,
                'endereco_funcional' => [
                    'logradouro' => $endereco->end_tipo_logradouro . ' ' . $endereco->end_logradouro,
                    'numero' => $endereco->end_numero,
                    'bairro' => $endereco->end_bairro,
                    'cidade' => $endereco->cidade ? $endereco->cidade->cid_nome : null,
                    'unidade' => [
                        'nome' => $lotacaoAtual->unidade->unid_nome,
                        'sigla' => $lotacaoAtual->unidade->unid_sigla,
                    ],
                ],
            ];
        });

        return response()->json([
            'resultados' => $result,
            'total' => $result->count(),
        ]);
    }

    /**
     * Gerar URL temporária para acesso a uma imagem no MinIO
     */
    private function generateTemporaryUrl($path)
    {
        // Verificar se estamos usando o driver s3 (MinIO)
        if (config('filesystems.default') === 's3') {
            // Gerar URL temporária com expiração de 5 minutos
            return Storage::temporaryUrl(
                $path,
                now()->addMinutes(5)
            );
        }

        // Fallback para ambiente local/desenvolvimento
        return url(Storage::url($path));
    }
}
