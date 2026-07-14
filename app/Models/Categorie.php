<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'logo',
        'est_disponible',
        'qualite',
    ];

    protected $casts = [
        'est_disponible' => 'boolean',
    ];

    /**
     * Relation avec les plats
     */
    public function plats()
    {
        return $this->hasMany(Plat::class, 'categorie_id');
    }

    /**
     * Nombre de plats dans cette catégorie
     */
    public function nombrePlats()
    {
        return $this->plats()->count();
    }
}
