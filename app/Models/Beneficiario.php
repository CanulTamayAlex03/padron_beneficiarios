<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Beneficiario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'beneficiarios';

    protected $fillable = [
        'curp',
        'primer_apellido',
        'segundo_apellido',
        'nombres',
        'fecha_nac',
        'estado_id',
        'sexo',
        'discapacidad',
        'indigena',
        'maya_hablante',
        'afromexicano',
        'estado_civil',
        'ocupacion_id',

        'calle',
        'numero',
        'letra',
        'cruzamiento_1',
        'cruzamiento_2',
        'tipo_asentamiento',
        'estado_viv_id',
        'municipio_id',
        'localidad',
        'colonia_fracc',
        'cp',
        'telefono',
        'referencias_domicilio'
    ];

    protected $casts = [
        'fecha_nac'     => 'date',
        'discapacidad'  => 'boolean',
        'indigena'      => 'boolean',
        'maya_hablante' => 'boolean',
        'afromexicano'  => 'boolean',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];


    // Relación conn estudios socioeconómicos
    public function estudiosSocioeconomicos()
    {
        return $this->hasMany(EstudioSocioeconomico::class, 'beneficiario_id');
    }

    // Relación con el estado de residencia
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id', 'id_estado');
    }

    // Relación con el estado de la vivienda
    public function estadoViv()
    {
        return $this->belongsTo(Estado::class, 'estado_viv_id', 'id_estado');
    }

    // Relación con el municipio
    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }

    // Relación con la ocupación
    public function ocupacion()
    {
        return $this->belongsTo(Ocupacion::class, 'ocupacion_id');
    }

    public function familiares()
    {
        return $this->hasMany(BeneficiarioFamiliar::class, 'beneficiario_id');
    }
}
