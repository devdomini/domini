<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
        'name',
        'address',
        'city',
        'country',
        'latitude',
        'longitude',
        'phone',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_active' => 'boolean',
    ];

    public function communes()
    {
        return $this->hasMany(Commune::class);
    }

    public function livreurs()
    {
        return $this->hasMany(User::class, 'warehouse_id')->where('role', 'livreur');
    }

    /**
     * Ordre de livraison des entreprises (communes rattachées à cet entrepôt).
     */
    public function trajetItems()
    {
        return $this->hasMany(WarehouseTrajetItem::class)->orderBy('position');
    }
}

