<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Programa extends Model
{
    use SoftDeletes;

    protected $table = 'programa';
    
    protected $fillable = ['nombre_programa'];
    
    protected $dates = ['deleted_at']; 

    public function tiposPrograma(): BelongsToMany
    {
        return $this->belongsToMany(
            TipoPrograma::class,
            'programa_tipo_programa',
            'programa_id',
            'tipo_programa_id'
        )->withTimestamps();
    }
}