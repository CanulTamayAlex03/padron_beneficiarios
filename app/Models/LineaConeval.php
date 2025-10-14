<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineaConeval extends Model
{
    use HasFactory;

    protected $table = 'lineas_coneval';

    protected $fillable = [
        'zona',
        'cantidad',
        'periodo',
        'descripcion'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'periodo' => 'date'
    ];


    public function estudiosSocioeconomicos()
    {
        return $this->hasMany(EstudioSocioeconomico::class, 'linea_coneval_id');
    }
}