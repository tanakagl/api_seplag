<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pessoa extends Model
{

    protected $table = 'pessoa';
    protected $primaryKey = 'pes_id';

    protected $fillable = [
        'pes_nome',
        'pes_data_nascimento',
        'pes_sexo',
        'pes_mae',
        'pes_pai',
    ];

    protected $casts = [
        'pes_data_nascimento' => 'date',
    ];

    public function servidorEfetivo()
    {
        return $this->hasOne(ServidorEfetivo::class, 'pes_id', 'pes_id');
    }

    public function servidorTemporario()
    {
        return $this->hasOne(ServidorTemporario::class, 'pes_id', 'pes_id');
    }

    public function enderecos()
    {
        return $this->belongsToMany(Endereco::class, 'pessoa_endereco', 'pes_id', 'end_id')
        ->withTimestamps();
    }

    public function lotacoes()
    {
        return $this->hasMany(Lotacao::class, 'pes_id', 'pes_id');
    }

    public function fotografias()
    {
        return $this->hasMany(Fotografia::class, 'pes_id', 'pes_id');
    }

    public function isServidorEfetivo()
    {
        return $this->servidorEfetivo()->exists();
    }

    public function isServidorTemporario()
    {
        return $this->servidorTemporario()->exists();
    }
}