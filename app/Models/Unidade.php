<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unidade extends Model
{
    protected $table = 'unidade';
    protected $primaryKey = 'uni_id';
    public $incrementing = false;
    
    protected $fillable = [
        'unid_nome',
        'unid_sigla',
    ];
    
    public function lotacao()
    {
        return $this->hasMany(Lotacao::class, 'unid_id', 'uni_id');
    }
}
