<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Abonnement extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_entreprise',
        'representant',
        'numero',
        'fonction',
        'status',
        'statut_subvention_commande',
        'pourcentage',
        'nbre_employe',
        'date_debut',
        'date_fin',
        'date_resiliation',
        'raison_resiliation',
    ];

    protected $casts = [
        'pourcentage' => 'decimal:2',
        'nbre_employe' => 'integer',
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_resiliation' => 'date',
    ];

    /**
     * Relation avec l'entreprise
     */
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'id_entreprise');
    }

    /**
     * Vérifier si l'abonnement est actif
     */
    public function estActif()
    {
        return $this->status === 'actif' 
            && $this->date_debut <= Carbon::today() 
            && $this->date_fin >= Carbon::today();
    }

    /**
     * Vérifier si l'abonnement est expiré
     */
    public function estExpire()
    {
        return $this->date_fin < Carbon::today();
    }

    /**
     * Jours restants
     */
    public function joursRestants()
    {
        if ($this->estExpire()) {
            return 0;
        }
        return Carbon::today()->diffInDays($this->date_fin);
    }

    /**
     * Résilier l'abonnement
     */
    public function resilier($raison = null)
    {
        $this->update([
            'status' => 'resilie',
            'date_resiliation' => Carbon::today(),
            'raison_resiliation' => $raison,
        ]);
    }

    /**
     * Suspendre l'abonnement
     */
    public function suspendre()
    {
        $this->update(['status' => 'suspendu']);
    }

    /**
     * Réactiver l'abonnement
     */
    public function reactiver()
    {
        if (!$this->estExpire()) {
            $this->update(['status' => 'actif']);
        }
    }

    /**
     * Renouveler l'abonnement
     */
    public function renouveler($duree_mois = 12)
    {
        $nouvelleDateDebut = Carbon::parse($this->date_fin)->addDay();
        $nouvelleDateFin = $nouvelleDateDebut->copy()->addMonths((int) $duree_mois);

        $this->update([
            'status' => 'actif',
            'date_debut' => $nouvelleDateDebut,
            'date_fin' => $nouvelleDateFin,
            'date_resiliation' => null,
            'raison_resiliation' => null,
        ]);
    }
}
