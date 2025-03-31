<?php

namespace App\Http\Controllers;

use App\Models\ServidorTemporario;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServidorTemporarioController extends Controller
{
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
            'st_data_demissao' => 'required|date',
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
}
