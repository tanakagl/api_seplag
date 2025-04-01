<?php

namespace App\Http\Controllers;

use App\Models\Lotacao;
use App\Models\Pessoa;
use App\Models\Unidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LotacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lotacoes = Lotacao::with(['pessoa', 'unidade'])
            ->orderBy('lot_data_lotacao', 'desc')
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
        $pessoas = Pessoa::select('pes_id', 'pes_nome')
            ->orderBy('pes_nome')
            ->get();
            
        $unidades = Unidade::select('unid_id', 'unid_nome', 'unid_sigla')
            ->orderBy('unid_nome')
            ->get();
        
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
        $request->validate([
            'pes_id' => 'required|exists:pessoa,pes_id',
            'unid_id' => 'required|exists:unidade,unid_id',
            'lot_data_lotacao' => 'required|date',
            'lot_data_remocao' => 'nullable|date|after_or_equal:lot_data_lotacao',
            'lot_portaria' => 'nullable|string|max:100',
        ]);

        // Verificar se já existe uma lotação ativa para esta pessoa
        $lotacaoAtiva = Lotacao::where('pes_id', $request->pes_id)
            ->whereNull('lot_data_remocao')
            ->first();

        // Se existir uma lotação ativa e a nova lotação não tem data de remoção,
        // atualizar a lotação anterior com a data de remoção igual à data de lotação da nova
        if ($lotacaoAtiva && !$request->lot_data_remocao) {
            $lotacaoAtiva->update([
                'lot_data_remocao' => $request->lot_data_lotacao
            ]);
        }

        Lotacao::create([
            'pes_id' => $request->pes_id,
            'unid_id' => $request->unid_id,
            'lot_data_lotacao' => $request->lot_data_lotacao,
            'lot_data_remocao' => $request->lot_data_remocao,
            'lot_portaria' => $request->lot_portaria,
        ]);
        
        return redirect()->route('lotacao.index')
                        ->with('success', 'Lotação cadastrada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Lotacao $lotacao)
    {
        $lotacao->load(['pessoa', 'unidade']);
        
        return Inertia::render('Lotacao/Show', [
            'lotacao' => $lotacao
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lotacao $lotacao)
    {
        $lotacao->load(['pessoa', 'unidade']);
        
        $pessoas = Pessoa::select('pes_id', 'pes_nome')
            ->orderBy('pes_nome')
            ->get();
            
        $unidades = Unidade::select('unid_id', 'unid_nome', 'unid_sigla')
            ->orderBy('unid_nome')
            ->get();
        
        return Inertia::render('Lotacao/Edit', [
            'lotacao' => $lotacao,
            'pessoas' => $pessoas,
            'unidades' => $unidades
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lotacao $lotacao)
    {
        $request->validate([
            'pes_id' => 'required|exists:pessoa,pes_id',
            'unid_id' => 'required|exists:unidade,unid_id',
            'lot_data_lotacao' => 'required|date',
            'lot_data_remocao' => 'nullable|date|after_or_equal:lot_data_lotacao',
            'lot_portaria' => 'nullable|string|max:100',
        ]);

        // Se a lotação estava sem data de remoção e agora tem, verificar se há outra lotação para atualizar
        if ($lotacao->lot_data_remocao === null && $request->lot_data_remocao !== null) {
            // Verificar se existe uma lotação posterior a esta
            $proximaLotacao = Lotacao::where('pes_id', $lotacao->pes_id)
                ->where('lot_data_lotacao', '>', $lotacao->lot_data_lotacao)
                ->orderBy('lot_data_lotacao', 'asc')
                ->first();

            // Se a data de remoção é posterior à data de lotação da próxima lotação, ajustar
            if ($proximaLotacao && $request->lot_data_remocao > $proximaLotacao->lot_data_lotacao) {
                return redirect()->back()
                    ->withErrors(['lot_data_remocao' => 'A data de remoção não pode ser posterior à data de lotação da próxima lotação.']);
            }
        }

        $lotacao->update([
            'pes_id' => $request->pes_id,
            'unid_id' => $request->unid_id,
            'lot_data_lotacao' => $request->lot_data_lotacao,
            'lot_data_remocao' => $request->lot_data_remocao,
            'lot_portaria' => $request->lot_portaria,
        ]);
        
        return redirect()->route('lotacao.index')
                        ->with('success', 'Lotação atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lotacao $lotacao)
    {
        try {
            $lotacao->delete();
            
            return redirect()->route('lotacao.index')
                            ->with('success', 'Lotação excluída com sucesso.');
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir lotação: ' . $e->getMessage());
            
            return redirect()->route('lotacao.index')
                            ->with('error', 'Erro ao excluir lotação: ' . $e->getMessage());
        }
    }
}
