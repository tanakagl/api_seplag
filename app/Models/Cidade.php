<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cidade extends Model
{
    protected $table = 'cidade';
    protected $primaryKey = 'cid_id';
    
    protected $fillable = [
        'cid_nome',
        'cid_uf',
    ];

    public function enderecos()
    {
        return $this->hasMany(Endereco::class, 'cid_id', 'cid_id');
    }
}
