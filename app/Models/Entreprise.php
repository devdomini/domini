<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'adresse',
        'lat',
        'long',
        'numero',
        'pays',
        'ville',
        'logo',
        'statut',
    ];

    protected $casts = [
        'statut' => 'boolean',
        'lat' => 'decimal:8',
        'long' => 'decimal:8',
    ];

    /**
     * Relation avec les utilisateurs (employés)
     */
    public function employes()
    {
        return $this->hasMany(User::class, 'id_entreprise');
    }

    /**
     * Nombre d'employés actifs
     */
    public function nombreEmployesActifs()
    {
        return $this->employes()->where('is_active', true)->count();
    }

    /**
     * Relation many-to-many avec les livreurs
     */
    public function livreurs()
    {
        return $this->belongsToMany(User::class, 'entreprise_livreur', 'entreprise_id', 'user_id')
                    ->where('role', 'livreur')
                    ->withTimestamps();
    }
}
