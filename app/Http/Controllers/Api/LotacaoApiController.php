<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lotacao;
use App\Models\Pessoa;
use App\Models\Unidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LotacaoApiController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $lotacoes = Lotacao::with(['pessoa', 'unidade'])->paginate($perPage);
        
        return response()->json($lotacoes);
    }

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

    public function show(Lotacao $lotacao)
    {
        $lotacao->load(['pessoa', 'unidade']);
        
        return response()->json($lotacao);
    }

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
    
    public function lotacoesPorPessoa(Pessoa $pessoa)
    {
        $lotacoes = $pessoa->lotacoes()->with('unidade')->get();
        
        return response()->json($lotacoes);
    }
    
    public function unidades()
    {
        $unidades = Unidade::orderBy('unid_nome')->get();
        
        return response()->json($unidades);
    }
}
