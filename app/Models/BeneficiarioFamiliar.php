<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeneficiarioFamiliar extends Model
{
    use HasFactory;

    protected $table = 'beneficiario_familiar';

    protected $fillable = [
        'nombres',
        'primer_apellido',
        'segundo_apellido',
        'curp',
        'telefono',
        'parentesco_id',
        'beneficiario_id',
    ];

    public function beneficiario()
    {
        return $this->belongsTo(Beneficiario::class, 'beneficiario_id');
    }

    public function parentesco()
    {
        return $this->belongsTo(Parentesco::class, 'parentesco_id');
    }
}
