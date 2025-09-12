<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiario extends Model
{
    use HasFactory;

    protected $table = 'beneficiarios';

    protected $fillable = [
        'curp',
        'primer_apellido',
        'segundo_apellido',
        'nombres',
        'fecha_nac',
        'estado_nac',
        'sexo',
        'discapacidad',
        'indigena',
        'maya_hablante',
        'afromexicano',
        'estado_civil',
        'ocupacion',
    ];

    protected $casts = [
        'fecha_nac'     => 'date',
        'discapacidad'  => 'boolean',
        'indigena'      => 'boolean',
        'maya_hablante' => 'boolean',
        'afromexicano'  => 'boolean',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];
}
