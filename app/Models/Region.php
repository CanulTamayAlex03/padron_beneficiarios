<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    use SoftDeletes;

    protected $table = 'region';
    
    protected $fillable = [
        'nombre_region',
        'activo'
    ];
    
    protected $casts = [
        'activo' => 'boolean'
    ];
    
    protected $dates = ['deleted_at'];

    public function estudiosSocioeconomicos(): HasMany
    {
        return $this->hasMany(EstudioSocioeconomico::class);
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }
}