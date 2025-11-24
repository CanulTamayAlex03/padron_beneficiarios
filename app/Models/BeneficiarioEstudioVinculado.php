<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiarioEstudioVinculado extends Model
{
    protected $table = 'beneficiario_estudio_vinculados';
    
    protected $fillable = [
        'estudio_socioeconomico_id',
        'beneficiario_vinculado_id', 
        'beneficiario_principal_id'
    ];
    
    public function estudio()
    {
        return $this->belongsTo(EstudioSocioeconomico::class, 'estudio_socioeconomico_id');
    }
    
    public function beneficiarioVinculado()
    {
        return $this->belongsTo(Beneficiario::class, 'beneficiario_vinculado_id');
    }
    
    public function beneficiarioPrincipal()
    {
        return $this->belongsTo(Beneficiario::class, 'beneficiario_principal_id');
    }
}