<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServidorEfetivo;
use App\Models\Unidade;
use App\Models\Lotacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ServidorEfetivoApiController extends Controller
{
    /**
     * Consultar servidores efetivos por unidade
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
            })
            ->get();
        
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
                })
                ->first();
            
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
     * Consultar endereço funcional por nome parcial do servidor
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
        })
        ->with(['pessoa.lotacoes.unidade.endereco'])
        ->get();
        
        // Formatar os dados para retornar apenas os endereços funcionais
        $result = $servidores->map(function ($servidor) {
            // Buscar lotação atual
            $lotacaoAtual = $servidor->pessoa->lotacoes()
                ->with(['unidade.endereco'])
                ->where(function ($query) {
                    $query->whereNull('lot_data_remocao')
                        ->orWhere('lot_data_remocao', '>=', now());
                })
                ->first();
            
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
