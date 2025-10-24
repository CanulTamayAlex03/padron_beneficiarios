<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parentesco extends Model
{
    use HasFactory;

    protected $table = 'parentesco';

    protected $fillable = [
        'descripcion'
    ];

    public function familiares()
    {
        return $this->hasMany(BeneficiarioFamiliar::class, 'parentesco_id');
    }
}