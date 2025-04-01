<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadeEndereco extends Model
{
    protected $table = 'unidade_endereco';
    protected $primaryKey = 'ue_id';
    
    protected $fillable = [
        'unid_id',
        'end_id',
    ];

    public function unidade()
    {
        return $this->belongsTo(Unidade::class, 'unid_id', 'unid_id');
    }

    public function endereco()
    {
        return $this->belongsTo(Endereco::class, 'end_id', 'end_id');
    }
}
