<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemCommande extends Model
{
    protected $fillable = [
        'commande_id',
        'plat_id',
        'accompagnements',
        'options',
        'prix',
        'is_subventionne',
        'quantite',
    ];

    protected $casts = [
        'accompagnements' => 'array',
        'options' => 'array',
        'prix' => 'decimal:2',
        'is_subventionne' => 'boolean',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function plat()
    {
        return $this->belongsTo(Plat::class);
    }
}
