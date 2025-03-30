<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lotacao extends Model
{
    protected $table = 'lotacao';
    protected $primaryKey = 'lot_id';
    public $incrementing = false;
    
    protected $fillable = [
        'pes_id',
        'unid_id',
        'lot_data_lotacao',
        'lot_data_remocao',
        'lot_portaria',
    ];

    protected $casts = [
        'lot_data_lotacao' => 'date',
        'lot_data_remocao' => 'date',
    ];
    
    public function unidade()
    {
        return $this->belongsTo(Unidade::class, 'unid_id', 'uni_id');
    }
    
    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pes_id', 'pes_id');
    }
}
