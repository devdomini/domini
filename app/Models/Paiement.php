<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = [
        'type',
        'montant',
        'ref',
        'mode_paiement',
        'statut',
        'commande_id',
        'user_id',
        'description',
        'metadata',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Générer une référence unique
    public static function generateRef()
    {
        return 'PAY-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
