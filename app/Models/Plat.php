<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plat extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prix',
        'image',
        'est_disponible',
        'detail',
        'categorie_id',
        'qualite',
    ];

    protected $casts = [
        'prix' => 'decimal:2',
        'est_disponible' => 'boolean',
    ];

    /**
     * Relation avec la catégorie
     */
    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    /**
     * Relation avec les accompagnements
     */
    public function accompagnements()
    {
        return $this->hasMany(Accompagnement::class);
    }

    /**
     * Relation avec les options
     */
    public function options()
    {
        return $this->hasMany(Option::class);
    }
}
