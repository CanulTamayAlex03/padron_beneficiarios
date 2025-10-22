<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'tipo_programa_id',
        'linea_coneval_id',
        'coneval_active',
        'servicio_salud_id',
        'escolaridad_id',
        'tipo_piso',
        'tipo_techo',
        'agua_alimentos',
        'medio_cocina',
        'vivienda',
        'servicio_sanitario',
        'electricidad',
        'cuartos_dormir',
        'razon_mayor',

        'preocupa_sin_alimentos',
        'alimentos_no_alcanzaron',
        'dieta_poco_variada_adultos',
        'adultos_comieron_menos',
        'adultos_hambre_sin_comer',
        'adultos_dejaron_comer_dia',

        'menores_dieta_poco_variada',
        'menores_comieron_menos',
        'menores_hambre_sin_comer',
        'menores_sin_comer_dia',
        'menores_sin_alimentos_saludables',
        'menores_dieta_alimentos_baratos',

        'res_estudio_1',
        'res_estudio_2',
        'res_estudio_3',
        'res_total'
    ];

    protected $dates = ['fecha_solicitud', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'electricidad' => 'boolean',
        'coneval_active' => 'boolean',
        'razon_mayor' => 'boolean',
        'cuartos_dormir' => 'integer',
        'fecha_solicitud' => 'date',

        'preocupa_sin_alimentos' => 'boolean',
        'alimentos_no_alcanzaron' => 'boolean',
        'dieta_poco_variada_adultos' => 'boolean',
        'adultos_comieron_menos' => 'boolean',
        'adultos_hambre_sin_comer' => 'boolean',
        'adultos_dejaron_comer_dia' => 'boolean',

        'menores_dieta_poco_variada' => 'boolean',
        'menores_comieron_menos' => 'boolean',
        'menores_hambre_sin_comer' => 'boolean',
        'menores_sin_comer_dia' => 'boolean',
        'menores_sin_alimentos_saludables' => 'boolean',
        'menores_dieta_alimentos_baratos' => 'boolean'
    ];

    public function integrantesHogar()
    {
        return $this->hasMany(IntegranteHogar::class, 'estudio_socioeconomico_id');
    }

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

    public function lineaConeval()
    {
        return $this->belongsTo(LineaConeval::class, 'linea_coneval_id');
    }

    public function servicioSalud()
    {
        return $this->belongsTo(ServicioSalud::class, 'servicio_salud_id');
    }

    public function escolaridad()
    {
        return $this->belongsTo(Escolaridad::class, 'escolaridad_id');
    }
}
