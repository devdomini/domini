<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Livraison extends Model
{
    protected $fillable = [
        'commande_id',
        'livreur_id',
        'client_id',
        'montant_livraison',
        'statut',
        'heure_assignation',
        'heure_prise_en_charge',
        'heure_livraison',
        'commentaire',
    ];

    protected $casts = [
        'montant_livraison' => 'decimal:2',
        'heure_assignation' => 'datetime',
        'heure_prise_en_charge' => 'datetime',
        'heure_livraison' => 'datetime',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function livreur()
    {
        return $this->belongsTo(User::class, 'livreur_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
