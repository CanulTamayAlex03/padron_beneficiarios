<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;

    protected $table = 'municipios';

    protected $fillable = [
        'descripcion',
        'estado_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id', 'id_estado');
    }

    public function beneficiarios()
    {
        return $this->hasMany(Beneficiario::class, 'municipio_id');
    }
}