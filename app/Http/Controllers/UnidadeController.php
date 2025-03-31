<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unidade;
use Inertia\Inertia;

class UnidadeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $unidades = Unidade::paginate(10);
        return Inertia::render('Unidade/Index', ['unidades' => $unidades]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Unidade/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Defina suas regras de validação aqui
        ]);

        Unidade::create($validated);

        return redirect()->route('unidades.index')
            ->with('success', 'Unidade criada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $unidade = Unidade::findOrFail($id);
        return Inertia::render('Unidade/Show', ['unidade' => $unidade]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $unidade = Unidade::findOrFail($id);
        return Inertia::render('Unidade/Edit', ['unidade' => $unidade]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $unidade = Unidade::findOrFail($id);
        
        $validated = $request->validate([
            // Defina suas regras de validação aqui
        ]);

        $unidade->update($validated);

        return redirect()->route('unidades.index')
            ->with('success', 'Unidade atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $unidade = Unidade::findOrFail($id);
        $unidade->delete();

        return redirect()->route('unidades.index')
            ->with('success', 'Unidade excluída com sucesso.');
    }
}
