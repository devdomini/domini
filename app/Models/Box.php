<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    use HasFactory;

    protected $fillable = [
        'ref',
        'nom',
        'id_entreprise',
        'lat',
        'long',
        'adresse',
        'capacite',
        'est_actif',
    ];

    protected $casts = [
        'lat' => 'decimal:7',
        'long' => 'decimal:7',
        'capacite' => 'integer',
        'est_actif' => 'boolean',
    ];

    /**
     * Relation avec l'entreprise
     */
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'id_entreprise');
    }

    /**
     * Relation avec les casiers
     */
    public function casiers()
    {
        return $this->hasMany(Casier::class, 'id_box');
    }

    /**
     * Nombre de casiers libres
     */
    public function casiersLibres()
    {
        return $this->casiers()->where('statut', 'libre')->count();
    }

    /**
     * Nombre de casiers occupés
     */
    public function casiersOccupes()
    {
        return $this->casiers()->where('statut', 'occupe')->count();
    }

    /**
     * Générer une référence unique
     */
    public static function generateRef()
    {
        return 'BOX-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
