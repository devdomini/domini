<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'telephone',
        'id_entreprise',
        'warehouse_id',
        'type_livreur',
        'num_box',
        'is_active',
        'is_dispo',
        'indispo_reason',
        'indispo_at',
        'code_verification',
        'code_expires_at',
        'telephone_verified_at',
        'current_lat',
        'current_long',
        'last_location_at',
        'fcm_token',
        'fcm_platform',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'fcm_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_dispo' => 'boolean',
            'indispo_at' => 'datetime',
            'code_expires_at' => 'datetime',
            'telephone_verified_at' => 'datetime',
            'current_lat' => 'decimal:8',
            'current_long' => 'decimal:8',
            'last_location_at' => 'datetime',
        ];
    }

    /**
     * Relation avec l'entreprise (ancienne, à garder pour compatibilité)
     */
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'id_entreprise');
    }

    /**
     * Relation many-to-many avec les entreprises (nouvelle)
     */
    public function entreprises()
    {
        return $this->belongsToMany(Entreprise::class, 'entreprise_livreur', 'user_id', 'entreprise_id')
                    ->withTimestamps();
    }

    /**
     * Relation avec les adresses de l'utilisateur
     */
    public function adresses()
    {
        return $this->hasMany(Adresse::class);
    }

    public function livraisons()
    {
        return $this->hasMany(Livraison::class, 'livreur_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
