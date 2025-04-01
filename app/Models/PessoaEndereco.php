<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PessoaEndereco extends Model
{
    protected $table = 'pessoa_endereco';
    protected $primaryKey = 'pe_id';
    public $timestamps = true;
    
    protected $fillable = [
        'pes_id',
        'end_id',
    ];

    /**
     * Relacionamento com a pessoa
     */
    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pes_id', 'pes_id');
    }

    /**
     * Relacionamento com o endereço
     */
    public function endereco()
    {
        return $this->belongsTo(Endereco::class, 'end_id', 'end_id');
    }
}
