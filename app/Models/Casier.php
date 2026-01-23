<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Casier extends Model
{
    use HasFactory;

    protected $fillable = [
        'ref',
        'qr_code',
        'id_box',
        'id_employe',
        'statut',
        'numero_casier',
    ];

    protected $casts = [
        'numero_casier' => 'integer',
    ];

    /**
     * Relation avec la box
     */
    public function box()
    {
        return $this->belongsTo(Box::class, 'id_box');
    }

    /**
     * Relation avec l'employé
     */
    public function employe()
    {
        return $this->belongsTo(User::class, 'id_employe');
    }

    /**
     * Générer une référence unique
     */
    public static function generateRef($boxRef, $numero)
    {
        return $boxRef . '-C' . str_pad($numero, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Générer un QR Code unique
     */
    public static function generateQRCode()
    {
        return 'QR-' . strtoupper(uniqid());
    }

    /**
     * Vérifier si le casier est disponible
     */
    public function estDisponible()
    {
        return $this->statut === 'libre';
    }
}
