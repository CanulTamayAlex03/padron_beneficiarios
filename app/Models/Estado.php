<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    use HasFactory;

    protected $table = 'estados';
    
    protected $primaryKey = 'id_estado';

    protected $fillable = [
        'nombre',
        'clave_estado'
    ];

    public function beneficiarios()
    {
        return $this->hasMany(Beneficiario::class, 'estado_id');
    }

    public function beneficiariosVivienda()
    {
        return $this->hasMany(Beneficiario::class, 'estado_viv_id');
    }

    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'estado_id');
    }
}