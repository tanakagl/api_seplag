<?php

namespace App\Http\Controllers;

use App\Models\Lotacao;
use App\Models\Pessoa;
use App\Models\Unidade;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LotacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lotacoes = Lotacao::with(['pessoa', 'unidade'])
            ->paginate(10);
            
        return Inertia::render('Lotacao/Index', [
            'lotacoes' => $lotacoes
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pessoas = Pessoa::all();
        $unidades = Unidade::all();
        
        return Inertia::render('Lotacao/Create', [
            'pessoas' => $pessoas,
            'unidades' => $unidades
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pes_id' => 'required|exists:pessoa,pes_id',
            'unid_id' => 'required|exists:unidade,uni_id',
            'lot_data_lotacao' => 'required|date',
            'lot_data_remocao' => 'nullable|date|after_or_equal:lot_data_lotacao',
            'lot_portaria' => 'nullable|string|max:255',
        ]);

        Lotacao::create($validated);

        return redirect()->route('lotacoes.index')
            ->with('success', 'Lotação criada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lotacao = Lotacao::with(['pessoa', 'unidade'])
            ->findOrFail($id);
            
        return Inertia::render('Lotacao/Show', [
            'lotacao' => $lotacao
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lotacao = Lotacao::findOrFail($id);
        $pessoas = Pessoa::all();
        $unidades = Unidade::all();
        
        return Inertia::render('Lotacao/Edit', [
            'lotacao' => $lotacao,
            'pessoas' => $pessoas,
            'unidades' => $unidades
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $lotacao = Lotacao::findOrFail($id);
        
        $validated = $request->validate([
            'pes_id' => 'required|exists:pessoa,pes_id',
            'unid_id' => 'required|exists:unidade,uni_id',
            'lot_data_lotacao' => 'required|date',
            'lot_data_remocao' => 'nullable|date|after_or_equal:lot_data_lotacao',
            'lot_portaria' => 'nullable|string|max:255',
        ]);

        $lotacao->update($validated);

        return redirect()->route('lotacoes.index')
            ->with('success', 'Lotação atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $lotacao = Lotacao::findOrFail($id);
        $lotacao->delete();

        return redirect()->route('lotacoes.index')
            ->with('success', 'Lotação excluída com sucesso.');
    }
    
    /**
     * Get lotações by pessoa ID.
     */
    public function getByPessoa(string $pesId)
    {
        $lotacoes = Lotacao::with('unidade')
            ->where('pes_id', $pesId)
            ->orderBy('lot_data_lotacao', 'desc')
            ->get();
            
        return response()->json($lotacoes);
    }
    
    /**
     * Get current lotação for pessoa.
     */
    public function getCurrentByPessoa(string $pesId)
    {
        $lotacao = Lotacao::with('unidade')
            ->where('pes_id', $pesId)
            ->whereNull('lot_data_remocao')
            ->first();
            
        return response()->json($lotacao);
    }
}
