<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use App\Models\Pessoa;
use App\Models\Cidade;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class EnderecoController extends Controller
{
    /**
     * Listar todos os endereços.
     */
    public function index()
    {
        $enderecos = Endereco::with('cidade')->paginate(10);
        
        return Inertia::render('Endereco/Index', [
            'enderecos' => $enderecos
        ]);
    }
    
    /**
     * Mostrar formulário para criar um novo endereço.
     */
    public function create()
    {
        $cidades = Cidade::orderBy('cid_nome')->get();
        
        return Inertia::render('Endereco/Create', [
            'cidades' => $cidades
        ]);
    }
    
    /**
     * Armazenar um novo endereço.
     */
    public function store(Request $request)
    {
        $request->validate([
            'end_tipo_logradouro' => 'required|string|max:50',
            'end_logradouro' => 'required|string|max:200',
            'end_numero' => 'nullable|integer',
            'end_bairro' => 'required|string|max:100',
            'cid_id' => 'required|exists:cidade,cid_id',
        ]);
        
        $endereco = Endereco::create($request->all());
        
        return redirect()->route('endereco.index')
                         ->with('success', 'Endereço criado com sucesso.');
    }
    
    /**
     * Mostrar detalhes de um endereço.
     */
    public function show(Endereco $endereco)
    {
        $endereco->load('cidade', 'pessoas');
        
        return Inertia::render('Endereco/Show', [
            'endereco' => $endereco
        ]);
    }
    
    /**
     * Mostrar formulário para editar um endereço.
     */
    public function edit(Endereco $endereco)
    {
        $cidades = Cidade::orderBy('cid_nome')->get();
        
        return Inertia::render('Endereco/Edit', [
            'endereco' => $endereco,
            'cidades' => $cidades
        ]);
    }
    
    /**
     * Atualizar um endereço existente.
     */
    public function update(Request $request, Endereco $endereco)
    {
        $request->validate([
            'end_tipo_logradouro' => 'required|string|max:50',
            'end_logradouro' => 'required|string|max:200',
            'end_numero' => 'nullable|integer',
            'end_bairro' => 'required|string|max:100',
            'cid_id' => 'required|exists:cidade,cid_id',
        ]);
        
        $endereco->update($request->all());
        
        return redirect()->route('endereco.index')
                         ->with('success', 'Endereço atualizado com sucesso.');
    }
    
    /**
     * Excluir um endereço.
     */
    public function destroy(Endereco $endereco)
    {
        // Remover todas as associações com pessoas
        $endereco->pessoas()->detach();
        
        // Excluir o endereço
        $endereco->delete();
        
        return redirect()->route('endereco.index')
                         ->with('success', 'Endereço excluído com sucesso.');
    }
    
    /**
     * Adicionar um endereço a uma pessoa.
     */
    public function addToPessoa(Request $request, Pessoa $pessoa)
    {
        $request->validate([
            'end_id' => 'required|exists:endereco,end_id',
        ]);
        
        // Verificar se a associação já existe
        if (!$pessoa->enderecos()->where('end_id', $request->end_id)->exists()) {
            $pessoa->enderecos()->attach($request->end_id);
            return redirect()->back()->with('success', 'Endereço adicionado à pessoa com sucesso.');
        }
        
        return redirect()->back()->with('info', 'Este endereço já está associado a esta pessoa.');
    }
    
    /**
     * Remover um endereço de uma pessoa.
     */
    public function removeFromPessoa(Pessoa $pessoa, Endereco $endereco)
    {
        $pessoa->enderecos()->detach($endereco->end_id);
        
        return redirect()->back()->with('success', 'Endereço removido da pessoa com sucesso.');
    }
    
    /**
     * Criar um novo endereço e associá-lo a uma pessoa.
     */
    public function createForPessoa(Pessoa $pessoa)
    {
        $cidades = Cidade::orderBy('cid_nome')->get();
        
        return Inertia::render('Endereco/CreateForPessoa', [
            'pessoa' => $pessoa,
            'cidades' => $cidades
        ]);
    }
    
    /**
     * Armazenar um novo endereço e associá-lo a uma pessoa.
     */
    public function storeForPessoa(Request $request, Pessoa $pessoa)
    {
        $request->validate([
            'end_tipo_logradouro' => 'required|string|max:50',
            'end_logradouro' => 'required|string|max:200',
            'end_numero' => 'nullable|integer',
            'end_bairro' => 'required|string|max:100',
            'cid_id' => 'required|exists:cidade,cid_id',
        ]);
        
        DB::transaction(function () use ($request, $pessoa) {
            // Criar o endereço
            $endereco = Endereco::create($request->all());
            
            // Associar à pessoa
            $pessoa->enderecos()->attach($endereco->end_id);
        });
        
        // Redirecionar com base no tipo de servidor
        if ($pessoa->servidorEfetivo) {
            return redirect()->route('servidores.efetivo.show', $pessoa->pes_id)
                            ->with('success', 'Endereço adicionado com sucesso.');
        } elseif ($pessoa->servidorTemporario) {
            return redirect()->route('servidores.temporario.show', $pessoa->pes_id)
                            ->with('success', 'Endereço adicionado com sucesso.');
        } else {
            return redirect()->back()->with('success', 'Endereço adicionado com sucesso.');
        }
    }
}
