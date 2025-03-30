<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServidorTemporario extends Model
{
    protected $table = 'servidor_temporario';
    protected $primaryKey = 'pes_id';
    public $incrementing = false;
    
    protected $fillable = [
        'pes_id',
        'st_data_admissao',
        'st_data_demissao',
    ];

    protected $casts = [
        'st_data_admissao' => 'date',
        'st_data_demissao' => 'date',
    ];
    
    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pes_id', 'pes_id');
    }
    
    public function lotacao()
    {
        return $this->hasMany(Lotacao::class, 'servidor_id', 'pes_id');
    }
}
