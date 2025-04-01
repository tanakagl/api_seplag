<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pessoa;
use App\Models\Fotografia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FotografiaApiController extends Controller
{
    /**
     * Upload de uma ou mais fotografias para um servidor
     */
    public function upload(Request $request)
    {
        $request->validate([
            'pes_id' => 'required|exists:pessoa,pes_id',
            'fotografias' => 'required|array',
            'fotografias.*' => 'required|image|max:5120', // Máximo 5MB por imagem
        ]);

        $pessoa = Pessoa::findOrFail($request->pes_id);
        $uploadedFiles = [];
        
        foreach ($request->file('fotografias') as $foto) {
            // Gerar nome único para o arquivo
            $filename = Str::uuid() . '.' . $foto->getClientOriginalExtension();
            
            // Definir o caminho no MinIO
            $path = 'fotografias/' . $pessoa->pes_id . '/' . $filename;
            
            // Fazer upload para o MinIO
            $uploaded = Storage::put($path, file_get_contents($foto));
            
            if ($uploaded) {
                // Salvar referência no banco de dados
                $fotografia = new Fotografia();
                $fotografia->pes_id = $pessoa->pes_id;
                $fotografia->fot_caminho = $path;
                $fotografia->fot_nome_original = $foto->getClientOriginalName();
                $fotografia->fot_tipo = $foto->getMimeType();
                $fotografia->fot_tamanho = $foto->getSize();
                $fotografia->save();
                
                // Gerar URL temporária
                $uploadedFiles[] = [
                    'id' => $fotografia->fot_id,
                    'nome_original' => $fotografia->fot_nome_original,
                    'url_temporaria' => $this->generateTemporaryUrl($path),
                ];
            }
        }
        
        return response()->json([
            'message' => 'Fotografias enviadas com sucesso',
            'fotografias' => $uploadedFiles,
        ]);
    }
    
    /**
     * Obter URL temporária para uma fotografia
     */
    public function getTemporaryUrl(Request $request)
    {
        $request->validate([
            'fot_id' => 'required|exists:fotografia,fot_id',
        ]);
        
        $fotografia = Fotografia::findOrFail($request->fot_id);
        
        return response()->json([
            'fotografia' => [
                'id' => $fotografia->fot_id,
                'nome_original' => $fotografia->fot_nome_original,
                'url_temporaria' => $this->generateTemporaryUrl($fotografia->fot_caminho),
            ],
        ]);
    }
    
    /**
     * Gerar URL temporária para acesso a uma imagem no MinIO
     */
    private function generateTemporaryUrl($path)
    {
        if (config('filesystems.default') === 's3') {
            // Gerar URL temporária com expiração de 5 minutos
            return Storage::temporaryUrl(
                $path,
                now()->addMinutes(5)
            );
        }
        
        return url(Storage::url($path));
    }
    
    /**
     * Listar todas as fotografias de uma pessoa
     */
    public function listByPessoa(Request $request)
    {
        $request->validate([
            'pes_id' => 'required|exists:pessoa,pes_id',
        ]);
        
        $fotografias = Fotografia::where('pes_id', $request->pes_id)->get();
        
        $result = $fotografias->map(function ($foto) {
            return [
                'id' => $foto->fot_id,
                'nome_original' => $foto->fot_nome_original,
                'tipo' => $foto->fot_tipo,
                'tamanho' => $foto->fot_tamanho,
                'url_temporaria' => $this->generateTemporaryUrl($foto->fot_caminho),
                'created_at' => $foto->created_at,
            ];
        });
        
        return response()->json([
            'fotografias' => $result,
            'total' => $result->count(),
        ]);
    }
    
    /**
     * Excluir uma fotografia
     */
    public function destroy($id)
    {
        $fotografia = Fotografia::findOrFail($id);
        
        // Excluir o arquivo do MinIO
        if (Storage::exists($fotografia->fot_caminho)) {
            Storage::delete($fotografia->fot_caminho);
        }
        
        // Excluir o registro do banco de dados
        $fotografia->delete();
        
        return response()->json([
            'message' => 'Fotografia excluída com sucesso',
        ]);
    }
}
