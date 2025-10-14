<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escolaridad extends Model
{
    use HasFactory;

    protected $table = 'escolaridad';

    protected $fillable = [
        'nombre_escolaridad',
        'puntos'
    ];

    public function estudiosSocioeconomicos()
    {
        return $this->hasMany(EstudioSocioeconomico::class, 'escolaridad_id');
    }
}