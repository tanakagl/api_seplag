<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use App\Models\ServidorEfetivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ServidorEfetivoController extends Controller
{
    public function index()
    {
        $servidores = ServidorEfetivo::with('pessoa')->paginate(10);
        
        return Inertia::render('ServidorEfetivo/Index', [
            'servidores' => $servidores
        ]);
    }

    public function create()
    {
        return Inertia::render('ServidorEfetivo/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'pes_id' => 'required|string|max:255',
            'pes_data_nascimento' => 'required|date',
            'pes_sexo' => 'required|string|max:1',
            'pes_mae' => 'required|string|max:255',
            'pes_pai' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            $pessoa = Pessoa::create([
                'pes_nome' => $request->pes_nome,
                'pes_data_nascimento' => $request->pes_data_nascimento,
                'pes_sexo' => $request->pes_sexo,
                'pes_mae' => $request->pes_mae,
                'pes_pai' => $request->pes_pai,
            ]);
            
            ServidorEfetivo::create([
                'pes_id' => $pessoa->pes_id,
                'se_matricula' => $request->se_matricula,
            ]);
        });
        
        return redirect()->route('servidores.efetivo.index')
                         ->with('success', 'Servidor efetivo cadastrado com sucesso.');
    }

    public function show(ServidorEfetivo $servidorEfetivo)
    {
        $servidorEfetivo->load('pessoa', 'pessoa.enderecos', 'pessoa.lotacoes.unidade');
        
        return Inertia::render('ServidorEfetivo/Show', [
            'servidor' => $servidorEfetivo
        ]);
    }

    public function edit(ServidorEfetivo $servidorEfetivo)
    {
        $servidorEfetivo->load('pessoa');
        
        return Inertia::render('ServidorEfetivo/Edit', [
            'servidor' => $servidorEfetivo
        ]);
    }

    public function update(Request $request, ServidorEfetivo $servidorEfetivo)
    {
        $request->validate([
            'pes_nome' => 'required|string|max:255',
            'pes_data_nascimento' => 'required|date',
            'pes_sexo' => 'required|string|max:1',
            'pes_mae' => 'required|string|max:255',
            'pes_pai' => 'required|string|max:255',
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
        
        return redirect()->route('servidores..index')
                         ->with('success', 'Servidor efetivo atualizado com sucesso.');
    }

    public function destroy(ServidorEfetivo $servidorEfetivo)
    {
        $pesId = $servidorEfetivo->pes_id;
        
        DB::transaction(function () use ($servidorEfetivo, $pesId) {
            $servidorEfetivo->delete();
            
            Pessoa::find($pesId)->delete();
        });
        
        return redirect()->route('servidores.efetivo.index')
                         ->with('success', 'Servidor efetivo excluído com sucesso.');
    }
}
