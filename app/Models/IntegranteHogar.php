<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntegranteHogar extends Model
{
    use SoftDeletes;

    protected $table = 'integrantes_hogar';

    protected $fillable = [
        'estudio_socioeconomico_id',
        'nombres',
        'apellidos',
        'edad',
        'parentesco_id',
        'ingreso_mensual'
    ];

    protected $casts = [
        'ingreso_mensual' => 'decimal:2',
    ];

    public function estudioSocioeconomico(): BelongsTo
    {
        return $this->belongsTo(EstudioSocioeconomico::class, 'estudio_socioeconomico_id');
    }

    public function parentesco(): BelongsTo
    {
        return $this->belongsTo(Parentesco::class, 'parentesco_id');
    }
}
