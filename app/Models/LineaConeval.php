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
        'descripcion',
        'activo'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'periodo' => 'date',
        'activo' => 'boolean'
    ];

    const ZONAS = ['Rural', 'Semiurbano', 'Urbano'];

    public function estudiosSocioeconomicos()
    {
        return $this->hasMany(EstudioSocioeconomico::class, 'linea_coneval_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopeInactivas($query)
    {
        return $query->where('activo', false);
    }

    public function scopePorPeriodo($query, $periodo)
    {
        return $query->where('periodo', $periodo);
    }

    public function scopeZona($query, $zona)
    {
        return $query->where('zona', $zona);
    }

    public function activar()
    {
        self::where('periodo', $this->periodo)
            ->where('zona', $this->zona)
            ->where('id', '!=', $this->id)
            ->update(['activo' => false]);
        
        $this->update(['activo' => true]);
    }


    public static function existeActiva($periodo, $zona, $excludeId = null)
    {
        $query = self::where('periodo', $periodo)
                    ->where('zona', $zona)
                    ->where('activo', true);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }


    public static function getConjuntoActivo()
    {
        return self::activas()
                    ->orderBy('periodo', 'desc')
                    ->orderBy('zona')
                    ->get()
                    ->groupBy('periodo')
                    ->first();
    }

    public static function periodoCompleto($periodo)
    {
        $zonasActivas = self::porPeriodo($periodo)
                            ->activas()
                            ->pluck('zona')
                            ->unique()
                            ->count();
        
        return $zonasActivas === count(self::ZONAS);
    }

    
}