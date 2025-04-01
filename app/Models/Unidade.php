<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unidade extends Model
{
    protected $table = 'unidade';
    protected $primaryKey = 'unid_id';
    public $timestamps = true;
    
    protected $fillable = [
        'unid_nome',
        'unid_sigla',
    ];

    /**
     * Relacionamento com lotações
     */
    public function lotacoes()
    {
        return $this->hasMany(Lotacao::class, 'unid_id', 'unid_id');
    }
    
    /**
     * Relacionamento com endereços através da tabela pivot
     */
    public function enderecos()
    {
        return $this->belongsToMany(Endereco::class, 'unidade_endereco', 'unid_id', 'end_id')
                    ->withTimestamps();
    }
    
    /**
     * Relacionamento direto com a tabela unidade_endereco
     */
    public function unidadeEnderecos()
    {
        return $this->hasMany(UnidadeEndereco::class, 'unid_id', 'unid_id');
    }
}
