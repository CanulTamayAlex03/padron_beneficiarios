<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TipoPrograma extends Model
{
    protected $table = 'tipo_programa';
    
    protected $fillable = ['nombre_tipo'];

    public function programas(): BelongsToMany
    {
        return $this->belongsToMany(
            Programa::class,
            'programa_tipo_programa',
            'tipo_programa_id',
            'programa_id'
        )->withTimestamps();
    }
}