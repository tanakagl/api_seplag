<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Verificar ServidorEfetivoApiController
$servidorEfetivoPath = app_path('Http/Controllers/Api/ServidorEfetivoApiController.php');
if (!file_exists($servidorEfetivoPath)) {
    echo "ServidorEfetivoApiController não encontrado, criando...\n";
    
    $content = <<<'EOD'
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServidorEfetivo;
use App\Models\Pessoa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     *     path="/servidores/efetivos",
     *     summary="Listar servidores efetivos",
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
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
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
     *     path="/servidores/efetivos/{id}",
     *     summary="Obter detalhes de um servidor efetivo",
     *     tags={"Servidores Efetivos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do servidor",
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
    public function show($id)
    {
        $servidor = ServidorEfetivo::with('pessoa', 'pessoa.enderecos', 'pessoa.lotacoes.unidade')
            ->findOrFail($id);
        return response()->json($servidor);
    }

    /**
     * @OA\Post(
     *     path="/servidores/efetivos",
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
     *     path="/servidores/efetivos/{id}",
     *     summary="Atualizar um servidor efetivo",
     *     tags={"Servidores Efetivos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do servidor",
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
    public function update(Request $request, $id)
    {
        $servidorEfetivo = ServidorEfetivo::findOrFail($id);
        
        $request->validate([
            'pes_nome' => 'required|string|max:255',
            'pes_data_nascimento' => 'required|date',
            'pes_sexo' => 'required|string|max:1',
            'pes_mae' => 'required|string|max:255',
            'pes_pai' => 'required|string|max:255',
            'se_matricula' => 'required|string|max:20|unique:servidor_efetivo,se_matricula,' . $id . ',pes_id',
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
     *     path="/servidores/efetivos/{id}",
     *     summary="Excluir um servidor efetivo",
     *     tags={"Servidores Efetivos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do servidor",
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
    public function destroy($id)
    {
        $servidorEfetivo = ServidorEfetivo::findOrFail($id);
        
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
                        
                        if (method_exists($pessoa, 'fotografias') && $pessoa->fotografias()->count() > 0) {
                            $pessoa->fotografias()->delete();
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
}
EOD;
    
    file_put_contents($servidorEfetivoPath, $content);
    echo "ServidorEfetivoApiController criado com sucesso.\n";
} else {
    echo "ServidorEfetivoApiController já existe.\n";
}

// Verificar FotografiaApiController
$fotografiaPath = app_path('Http/Controllers/Api/FotografiaApiController.php');
if (!file_exists($fotografiaPath)) {
    echo "FotografiaApiController não encontrado, criando...\n";
    
    $content = <<<'EOD'
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fotografia;
use App\Models\Pessoa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @OA\Tag(
 *     name="Fotografias",
 *     description="Endpoints para gerenciamento de fotografias de servidores"
 * )
 */
class FotografiaApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/fotografias/pessoa/{pessoaId}",
     *     summary="Listar fotografias de uma pessoa",
     *     tags={"Fotografias"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="pessoaId",
     *         in="path",
     *         description="ID da pessoa",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de fotografias",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="fot_id", type="integer"),
     *                 @OA\Property(property="pes_id", type="integer"),
     *                 @OA\Property(property="fot_nome_arquivo", type="string"),
     *                 @OA\Property(property="fot_data_upload", type="string", format="date-time"),
     *                 @OA\Property(property="url", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pessoa não encontrada"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function listByPessoa($pessoaId)
    {
        $pessoa = Pessoa::findOrFail($pessoaId);
        $fotografias = Fotografia::where('pes_id', $pessoaId)->get();
        
        $fotografias->each(function ($foto) {
            $foto->url = route('api.fotografias.url', $foto->fot_id);
        });
        
        return response()->json($fotografias);
    }

    /**
     * @OA\Get(
     *     path="/fotografias/{id}/url",
     *     summary="Obter URL temporária para download de uma fotografia",
     *     tags={"Fotografias"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da fotografia",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="URL temporária para download",
     *         @OA\JsonContent(
     *             @OA\Property(property="url", type="string"),
     *             @OA\Property(property="expires_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Fotografia não encontrada"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function getTemporaryUrl($id)
    {
        $fotografia = Fotografia::findOrFail($id);
        
        // Gerar URL temporária válida por 5 minutos
        $url = Storage::temporaryUrl(
            'fotografias/' . $fotografia->fot_nome_arquivo,
            now()->addMinutes(5)
        );
        
        return response()->json([
            'url' => $url,
            'expires_at' => now()->addMinutes(5)->toDateTimeString()
        ]);
    }

    /**
     * @OA\Post(
     *     path="/fotografias/upload",
     *     summary="Fazer upload de uma fotografia para uma pessoa",
     *     tags={"Fotografias"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="foto",
     *                     type="string",
     *                     format="binary",
     *                     description="Arquivo de imagem"
     *                 ),
     *                 @OA\Property(
     *                     property="pes_id",
     *                     type="integer",
     *                     description="ID da pessoa"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Fotografia enviada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="fot_id", type="integer"),
     *             @OA\Property(property="pes_id", type="integer"),
     *             @OA\Property(property="fot_nome_arquivo", type="string"),
     *             @OA\Property(property="fot_data_upload", type="string", format="date-time"),
     *             @OA\Property(property="url", type="string")
     *         )
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
    public function upload(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|max:2048', // Máximo 2MB
            'pes_id' => 'required|exists:pessoa,pes_id',
        ]);
        
        $pessoa = Pessoa::findOrFail($request->pes_id);
        
        // Gerar nome único para o arquivo
        $extension = $request->file('foto')->getClientOriginalExtension();
        $fileName = Str::uuid() . '.' . $extension;
        
        // Armazenar o arquivo
        $path = $request->file('foto')->storeAs('fotografias', $fileName);
        
        // Criar registro no banco de dados
        $fotografia = Fotografia::create([
            'pes_id' => $request->pes_id,
            'fot_nome_arquivo' => $fileName,
            'fot_data_upload' => now(),
        ]);
        
        $fotografia->url = route('api.fotografias.url', $fotografia->fot_id);
        
        return response()->json($fotografia, 201);
    }

    /**
     * @OA\Delete(
     *     path="/fotografias/{id}",
     *     summary="Excluir uma fotografia",
     *     tags={"Fotografias"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da fotografia",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Fotografia excluída com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Fotografia não encontrada"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function destroy($id)
    {
        $fotografia = Fotografia::findOrFail($id);
        
        // Excluir o arquivo físico
        if (Storage::exists('fotografias/' . $fotografia->fot_nome_arquivo)) {
            Storage::delete('fotografias/' . $fotografia->fot_nome_arquivo);
        }
        
        // Excluir o registro do banco de dados
        $fotografia->delete();
        
        return response()->json(['message' => 'Fotografia excluída com sucesso.']);
    }
}
EOD;
    
    file_put_contents($fotografiaPath, $content);
    echo "FotografiaApiController criado com sucesso.\n";
} else {
    echo "FotografiaApiController já existe.\n";
}

// Atualizar as rotas API
$apiRoutesPath = base_path('routes/api.php');
$apiRoutes = file_get_contents($apiRoutesPath);

if (strpos($apiRoutes, 'ServidorEfetivoApiController') === false || strpos($apiRoutes, 'FotografiaApiController') === false) {
    echo "Atualizando rotas API...\n";
    
    // Encontrar o final do grupo de middleware auth:sanctum
    $pos = strpos($apiRoutes, '});') + 3;
    
    // Adicionar as rotas faltantes
    $newRoutes = <<<'EOD'

// Rotas para ServidorEfetivo
Route::middleware(['auth:sanctum', 'ability:read'])->group(function () {
    Route::get('/servidores/efetivos', [App\Http\Controllers\Api\ServidorEfetivoApiController::class, 'index']);
    Route::get('/servidores/efetivos/{id}', [App\Http\Controllers\Api\ServidorEfetivoApiController::class, 'show']);
    
    Route::middleware('ability:create')->group(function () {
        Route::post('/servidores/efetivos', [App\Http\Controllers\Api\ServidorEfetivoApiController::class, 'store']);
    });
    
    Route::middleware('ability:update')->group(function () {
        Route::put('/servidores/efetivos/{id}', [App\Http\Controllers\Api\ServidorEfetivoApiController::class, 'update']);
    });
    
    Route::middleware('ability:delete')->group(function () {
        Route::delete('/servidores/efetivos/{id}', [App\Http\Controllers\Api\ServidorEfetivoApiController::class, 'destroy']);
    });
});

// Rotas para Fotografia
Route::middleware(['auth:sanctum', 'ability:read'])->group(function () {
    Route::get('/fotografias/pessoa/{pessoaId}', [App\Http\Controllers\Api\FotografiaApiController::class, 'listByPessoa'])->name('api.fotografias.list');
    Route::get('/fotografias/{id}/url', [App\Http\Controllers\Api\FotografiaApiController::class, 'getTemporaryUrl'])->name('api.fotografias.url');
    
    Route::middleware('ability:create')->group(function () {
        Route::post('/fotografias/upload', [App\Http\Controllers\Api\FotografiaApiController::class, 'upload']);
    });
    
    Route::middleware('ability:delete')->group(function () {
        Route::delete('/fotografias/{id}', [App\Http\Controllers\Api\FotografiaApiController::class, 'destroy']);
    });
});
EOD;
    
    // Inserir as novas rotas
    $apiRoutes = substr($apiRoutes, 0, $pos) . $newRoutes . substr($apiRoutes, $pos);
    file_put_contents($apiRoutesPath, $apiRoutes);
    
    echo "Rotas API atualizadas com sucesso.\n";
} else {
    echo "Rotas API já estão configuradas.\n";
}

echo "Processo concluído. Agora regenere a documentação Swagger.\n";
