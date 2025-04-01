<?php

namespace App\Http\Controllers;

use App\Models\Unidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UnidadeController extends Controller
{
    public function index()
    {
        $unidade = Unidade::paginate(10);
        
        return Inertia::render('Unidade/Index', [
            'unidade' => $unidade
        ]);
    }

    public function create()
    {
        return Inertia::render('Unidade/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'unid_nome' => 'required|string|max:255',
            'unid_sigla' => 'required|string|max:20',
        ]);

        Unidade::create([
            'unid_nome' => $request->unid_nome,
            'unid_sigla' => $request->unid_sigla,
        ]);
        
        return redirect()->route('unidade.index')
                        ->with('success', 'Unidade cadastrada com sucesso.');
    }

    public function show(Unidade $unidade)
    {
        $unidade->load('lotacao.pessoa');
        
        return Inertia::render('Unidade/Show', [
            'unidade' => $unidade
        ]);
    }

    public function edit(Unidade $unidade)
    {
        return Inertia::render('Unidade/Edit', [
            'unidade' => $unidade
        ]);
    }

    public function update(Request $request, Unidade $unidade)
    {
        $request->validate([
            'unid_nome' => 'required|string|max:255',
            'unid_sigla' => 'required|string|max:20',
        ]);

        $unidade->update([
            'unid_nome' => $request->unid_nome,
            'unid_sigla' => $request->unid_sigla,
        ]);
        
        return redirect()->route('unidade.index')
                        ->with('success', 'Unidade atualizada com sucesso.');
    }

    public function destroy(Unidade $unidade)
    {
        try {
            DB::transaction(function () use ($unidade) {
                if ($unidade->lotacoes()->count() > 0) {
                    throw new \Exception('Não é possível excluir esta unidade pois existem lotações vinculadas a ela.');
                }
                                if (method_exists($unidade, 'enderecos') && $unidade->enderecos()->count() > 0) {
                    $unidade->enderecos()->delete();
                }
                
                $unidade->delete();
            });
            
            return redirect()->route('unidade.index')
                            ->with('success', 'Unidade excluída com sucesso.');
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir unidade: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            return redirect()->route('unidade.index')
                            ->with('error', 'Não foi possível excluir a unidade. Erro: ' . $e->getMessage());
        }
    }
}
