<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Solicitud extends Model
{
    use SoftDeletes;

    protected $table = 'solicitud';
    
    protected $fillable = ['nombre_solicitud'];
    
    protected $dates = ['deleted_at'];

    
    public function estudiosSocioeconomicos(): HasMany
    {
        return $this->hasMany(EstudioSocioeconomico::class);
    }
}