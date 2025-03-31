<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use App\Models\ServidorTemporario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ServidorTemporarioController extends Controller
{
    public function index()
    {
        $servidores = ServidorTemporario::with('pessoa')->paginate(10);
        
        return Inertia::render('ServidorTemporario/Index', [
            'servidores' => $servidores
        ]);
    }

    public function create()
    {
        return Inertia::render('ServidorTemporario/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'pes_nome' => 'required|string|max:255',
            'pes_data_nascimento' => 'required|date',
            'pes_sexo' => 'required|string|max:1',
            'pes_mae' => 'required|string|max:255',
            'pes_pai' => 'required|string|max:255',
            'st_data_admissao' => 'required|date',
            'st_data_demissao' => 'nullable|date|after_or_equal:st_data_admissao',
        ]);

        DB::transaction(function () use ($request) {
            $pessoa = Pessoa::create([
                'pes_nome' => $request->pes_nome,
                'pes_data_nascimento' => $request->pes_data_nascimento,
                'pes_sexo' => $request->pes_sexo,
                'pes_mae' => $request->pes_mae,
                'pes_pai' => $request->pes_pai,
            ]);
            
            ServidorTemporario::create([
                'pes_id' => $pessoa->pes_id,
                'st_data_admissao' => $request->st_data_admissao,
                'st_data_demissao' => $request->st_data_demissao,
            ]);
        });
        
        return redirect()->route('servidores.temporario.index')
                        ->with('success', 'Servidor temporário cadastrado com sucesso.');
    }

    public function show(ServidorTemporario $servidorTemporario)
    {
        $servidorTemporario->load('pessoa', 'pessoa.enderecos', 'pessoa.lotacoes.unidade');
        
        return Inertia::render('ServidorTemporario/Show', [
            'servidor' => $servidorTemporario
        ]);
    }

    public function edit(ServidorTemporario $servidorTemporario)
    {
        $servidorTemporario->load('pessoa');
        
        if (!$servidorTemporario->pessoa) {
            return redirect()->route('servidores.temporario.index')
                             ->with('error', 'Dados da pessoa não encontrados para este servidor.');
        }
        
        return Inertia::render('ServidorTemporario/Edit', [
            'servidor' => $servidorTemporario
        ]);
    }

    public function update(Request $request, ServidorTemporario $servidorTemporario)
    {
        $request->validate([
            'pes_nome' => 'required|string|max:255',
            'pes_data_nascimento' => 'required|date',
            'pes_sexo' => 'required|string|max:1',
            'pes_mae' => 'required|string|max:255',
            'pes_pai' => 'required|string|max:255',
            'st_data_admissao' => 'required|date',
            'st_data_demissao' => 'nullable|date|after_or_equal:st_data_admissao',
        ]);

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

        return redirect()->route('servidores.temporario.index')
                         ->with('success', 'Servidor temporário atualizado com sucesso.');
    }

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
                        $pessoa->delete();
                    }
                }
            });
            
            return redirect()->route('servidores.temporario.index')
                            ->with('success', 'Servidor temporário excluído com sucesso.');
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir servidor: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            return redirect()->route('servidores.temporario.index')
                            ->with('error', 'Não foi possível excluir o servidor. Erro: ' . $e->getMessage());
        }
    }
}
