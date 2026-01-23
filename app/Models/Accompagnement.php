<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accompagnement extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'image',
        'qte_gratuit',
        'prix_unitaire',
        'disponible',
        'plat_id',
    ];

    protected $casts = [
        'qte_gratuit' => 'integer',
        'prix_unitaire' => 'decimal:2',
        'disponible' => 'boolean',
    ];

    /**
     * Relation avec le plat
     */
    public function plat()
    {
        return $this->belongsTo(Plat::class);
    }
}
