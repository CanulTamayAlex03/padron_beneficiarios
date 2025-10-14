<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicioSalud extends Model
{
    use HasFactory;

    protected $table = 'servicio_salud';

    protected $fillable = [
        'nombre_servicio',
        'puntos'
    ];

    
    public function estudiosSocioeconomicos()
    {
        return $this->hasMany(EstudioSocioeconomico::class, 'servicio_salud_id');
    }
}