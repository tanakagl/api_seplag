<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServidorEfetivo extends Model
{
    protected $table = 'servidor_efetivo';
    protected $primaryKey = 'pes_id';
    public $incrementing = false;

    protected $fillable = [
        'pes_id',
        'se_matrise_matricula',        
    ];

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pes_id', 'pes_id');
    }

    public function lotacao()
    {
        return $this->hasMany(Lotacao::class, 'servidor_id', 'pes_id')
        ->where('tipo_servidor', 'efetivo');
    }
}
