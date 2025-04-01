<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Endereco;
use App\Models\Pessoa;
use App\Models\Cidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EnderecoApiController extends Controller
{
    /**
     * Listar todos os endereços.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $enderecos = Endereco::with('cidade')->paginate($perPage);
        
        return response()->json($enderecos);
    }
    
    /**
     * Armazenar um novo endereço.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'end_tipo_logradouro' => 'required|string|max:50',
            'end_logradouro' => 'required|string|max:200',
            'end_numero' => 'nullable|integer',
            'end_bairro' => 'required|string|max:100',
            'cid_id' => 'required|exists:cidade,cid_id',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $endereco = Endereco::create($request->all());
        
        return response()->json([
            'message' => 'Endereço criado com sucesso.',
            'endereco' => $endereco
        ], 201);
    }
    
    /**
     * Mostrar detalhes de um endereço.
     */
    public function show(Endereco $endereco)
    {
        $endereco->load('cidade', 'pessoas');
        
        return response()->json($endereco);
    }
    
    /**
     * Atualizar um endereço existente.
     */
    public function update(Request $request, Endereco $endereco)
    {
        $validator = Validator::make($request->all(), [
            'end_tipo_logradouro' => 'required|string|max:50',
            'end_logradouro' => 'required|string|max:200',
            'end_numero' => 'nullable|integer',
            'end_bairro' => 'required|string|max:100',
            'cid_id' => 'required|exists:cidade,cid_id',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $endereco->update($request->all());
        
        return response()->json([
            'message' => 'Endereço atualizado com sucesso.',
            'endereco' => $endereco
        ]);
    }
    
    /**
     * Excluir um endereço.
     */
    public function destroy(Endereco $endereco)
    {
        try {
            // Remover todas as associações com pessoas
            $endereco->pessoas()->detach();
            
            // Excluir o endereço
            $endereco->delete();
            
            return response()->json([
                'message' => 'Endereço excluído com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao excluir endereço.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Adicionar um endereço a uma pessoa.
     */
    public function addToPessoa(Request $request, Pessoa $pessoa)
    {
        $validator = Validator::make($request->all(), [
            'end_id' => 'required|exists:endereco,end_id',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Verificar se a associação já existe
        if (!$pessoa->enderecos()->where('end_id', $request->end_id)->exists()) {
            $pessoa->enderecos()->attach($request->end_id);
            return response()->json([
                'message' => 'Endereço adicionado à pessoa com sucesso.'
            ]);
        }
        
        return response()->json([
            'message' => 'Este endereço já está associado a esta pessoa.'
        ], 409);
    }
    
    /**
     * Remover um endereço de uma pessoa.
     */
    public function removeFromPessoa(Pessoa $pessoa, Endereco $endereco)
    {
        $pessoa->enderecos()->detach($endereco->end_id);
        
        return response()->json([
            'message' => 'Endereço removido da pessoa com sucesso.'
        ]);
    }
    
    /**
     * Criar um novo endereço e associá-lo a uma pessoa.
     */
    public function storeForPessoa(Request $request, Pessoa $pessoa)
    {
        $validator = Validator::make($request->all(), [
            'end_tipo_logradouro' => 'required|string|max:50',
            'end_logradouro' => 'required|string|max:200',
            'end_numero' => 'nullable|integer',
            'end_bairro' => 'required|string|max:100',
            'cid_id' => 'required|exists:cidade,cid_id',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $endereco = null;
        
        DB::transaction(function () use ($request, $pessoa, &$endereco) {
            // Criar o endereço
            $endereco = Endereco::create($request->all());
            
            // Associar à pessoa
            $pessoa->enderecos()->attach($endereco->end_id);
        });
        
        return response()->json([
            'message' => 'Endereço adicionado com sucesso.',
            'endereco' => $endereco
        ], 201);
    }
    
    /**
     * Listar cidades para seleção
     */
    public function cidades()
    {
        $cidades = Cidade::orderBy('cid_nome')->get();
        
        return response()->json($cidades);
    }
}
