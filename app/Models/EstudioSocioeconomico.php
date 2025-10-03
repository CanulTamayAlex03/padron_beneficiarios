<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstudioSocioeconomico extends Model
{
    use SoftDeletes;

    protected $table = 'estudio_socioeconomico';
    
    protected $fillable = [
        'folio',
        'fecha_solicitud',
        'beneficiario_id',
        'region_id',
        'solicitud_id',
        'programa_id',
        'tipo_programa_id'
    ];

    protected $dates = ['fecha_solicitud', 'deleted_at'];

    public function beneficiario(): BelongsTo
    {
        return $this->belongsTo(Beneficiario::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function programa(): BelongsTo
    {
        return $this->belongsTo(Programa::class);
    }

    public function tipoPrograma(): BelongsTo
    {
        return $this->belongsTo(TipoPrograma::class, 'tipo_programa_id');
    }
}