<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fotografia extends Model
{
    use HasFactory;

    protected $table = 'fotografia';
    protected $primaryKey = 'fot_id';
    
    protected $fillable = [
        'pes_id',
        'fot_caminho',
        'fot_nome_original',
        'fot_tipo',
        'fot_tamanho',
    ];
    
    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pes_id', 'pes_id');
    }
}
