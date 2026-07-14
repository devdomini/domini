<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = [
        'ref',
        'id_employe',
        'montant_total',
        'is_lunch',
        'date_livraison',
        'creneau',
        'adresse_id',
        'statut_commande',
        'statut_preparation',
        'statut_livraison',
        'lieu',
        'lat',
        'long',
        'consigne_cuisinier',
        'consigne_livreur',
        'statut_paiement',
        'mode_paiement',
        'numero_telephone',
    ];

    protected $casts = [
        'montant_total' => 'decimal:2',
        'is_lunch' => 'boolean',
        'date_livraison' => 'date',
        'lat' => 'decimal:7',
        'long' => 'decimal:7',
    ];

    public function employe()
    {
        return $this->belongsTo(User::class, 'id_employe');
    }

    public function items()
    {
        return $this->hasMany(ItemCommande::class, 'commande_id');
    }

    public function livraison()
    {
        return $this->hasOne(Livraison::class, 'commande_id');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'commande_id');
    }

    public function adresse()
    {
        return $this->belongsTo(Adresse::class);
    }

    // Générer une référence unique
    public static function generateRef()
    {
        return 'CMD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
