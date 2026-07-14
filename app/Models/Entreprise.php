<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'commune_id',
        'commercial_id',
        'logo',
        'statut',
    ];

    protected $casts = [
        'statut' => 'boolean',
    ];

    /**
     * Affichage formulaire : pas de zéros finaux (ex. 5.3546081, pas 5.35460810).
     * Accepte la virgule décimale à l'enregistrement.
     */
    public static function formatCoordinateForInput(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $normalized = str_replace(',', '.', trim((string) $value));
        if ($normalized === '' || ! is_numeric($normalized)) {
            return null;
        }

        $formatted = rtrim(rtrim(sprintf('%.8F', (float) $normalized), '0'), '.');

        return match ($formatted) {
            '', '-0' => '0',
            default => $formatted,
        };
    }

    protected function lat(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => self::formatCoordinateForInput($value),
            set: fn ($value) => self::formatCoordinateForInput($value),
        );
    }

    protected function long(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => self::formatCoordinateForInput($value),
            set: fn ($value) => self::formatCoordinateForInput($value),
        );
    }

    /**
     * Relation avec les utilisateurs (employés)
     */
    public function commercial()
    {
        return $this->belongsTo(User::class, 'commercial_id');
    }

    public function employes()
    {
        return $this->hasMany(User::class, 'id_entreprise');
    }

    public function boxes()
    {
        return $this->hasMany(Box::class, 'id_entreprise');
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

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    public function warehouseTrajetItems()
    {
        return $this->hasMany(WarehouseTrajetItem::class);
    }
}

