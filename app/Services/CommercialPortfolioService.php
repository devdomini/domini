<?php

namespace App\Services;

use App\Models\Abonnement;
use App\Models\Box;
use App\Models\Casier;
use App\Models\Commande;
use App\Models\Entreprise;
use App\Models\Paiement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CommercialPortfolioService
{
    public function isCommercial(User $user): bool
    {
        return $user->role === 'commercial';
    }

    /** Toutes les entreprises sont visibles par le commercial (web + mobile). */
    public function entrepriseIdsForCommercial(User $commercial): Collection
    {
        return Entreprise::query()->pluck('id');
    }

    public function entreprisesQuery(User $commercial): Builder
    {
        return Entreprise::query()
            ->with(['commune.warehouse', 'commercial'])
            ->orderByDesc('created_at');
    }

    public function findEntrepriseForCommercial(int $id, User $commercial): Entreprise
    {
        return Entreprise::query()
            ->with(['commune.warehouse', 'commercial'])
            ->where('entreprises.id', $id)
            ->firstOrFail();
    }

    public function findBoxForCommercial(int $boxId, User $commercial): Box
    {
        return Box::query()
            ->where('boxes.id', $boxId)
            ->with(['entreprise.employes', 'entreprise.commercial', 'casiers.employe'])
            ->firstOrFail();
    }

    public function normalizeGpsOnRequest(Request $request): void
    {
        foreach (['lat', 'long'] as $key) {
            if (! $request->has($key)) {
                continue;
            }
            $raw = $request->input($key);
            if ($raw === null || $raw === '') {
                continue;
            }
            $formatted = Entreprise::formatCoordinateForInput($raw);
            if ($formatted !== null) {
                $request->merge([$key => $formatted]);
            }
        }
    }

    public function validateEntreprisePayload(Request $request, bool $forUpdate = false): array
    {
        $this->normalizeGpsOnRequest($request);

        $rules = [
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string',
            'ville' => 'required|string|max:255',
            'pays' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'lat' => 'nullable|numeric|between:-90,90',
            'long' => 'nullable|numeric|between:-180,180',
            'commune_id' => 'nullable|exists:communes,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        if ($forUpdate) {
            $rules['statut'] = 'nullable|boolean';
        }

        return $request->validate($rules);
    }

    public function handleLogoUpload(Request $request, ?Entreprise $existing = null): ?string
    {
        if (! $request->hasFile('logo')) {
            return null;
        }

        if ($existing?->logo) {
            Storage::disk('public')->delete($existing->logo);
        }

        return $request->file('logo')->store('logos', 'public');
    }

    public function createEntreprise(User $commercial, array $validated, Request $request): Entreprise
    {
        $validated['commercial_id'] = $commercial->id;
        $validated['statut'] = $validated['statut'] ?? true;
        $validated['pays'] = $validated['pays'] ?? "Côte d'Ivoire";

        if ($logo = $this->handleLogoUpload($request)) {
            $validated['logo'] = $logo;
        }

        return Entreprise::create($validated);
    }

    public function updateEntreprise(Entreprise $entreprise, array $validated, Request $request, ?User $commercial = null): Entreprise
    {
        if (array_key_exists('statut', $validated)) {
            $validated['statut'] = (bool) $validated['statut'];
        } elseif ($request->has('statut')) {
            $validated['statut'] = $request->boolean('statut');
        }

        if ($commercial && $entreprise->commercial_id === null) {
            $validated['commercial_id'] = $commercial->id;
        }

        if ($logo = $this->handleLogoUpload($request, $entreprise)) {
            $validated['logo'] = $logo;
        }

        $entreprise->update($validated);

        return $entreprise->fresh();
    }

    public function dashboardStats(User $commercial): array
    {
        $entrepriseIds = $this->entrepriseIdsForCommercial($commercial);
        $today = Carbon::today();
        $debutMois = $today->copy()->startOfMonth();
        $finMois = $today->copy()->endOfMonth();

        $entreprisesCount = $entrepriseIds->count();
        $entreprisesActives = Entreprise::whereIn('id', $entrepriseIds)->where('statut', true)->count();

        $employesQuery = User::where('role', 'employe')->whereIn('id_entreprise', $entrepriseIds);
        $employesCount = (clone $employesQuery)->count();
        $employesActifs = (clone $employesQuery)->where('is_active', true)->count();

        $boxesCount = Box::whereIn('id_entreprise', $entrepriseIds)->count();

        $casiersBase = Casier::whereHas('box', fn ($q) => $q->whereIn('id_entreprise', $entrepriseIds));
        $casiersCount = (clone $casiersBase)->count();
        $casiersLibres = (clone $casiersBase)->where('statut', 'libre')->count();
        $casiersOccupes = (clone $casiersBase)->where('statut', 'occupe')->count();
        $casiersHorsService = (clone $casiersBase)->where('statut', 'hors_service')->count();
        $tauxOccupation = $casiersCount > 0
            ? round(($casiersOccupes / $casiersCount) * 100, 1)
            : 0.0;

        $abonnementsBase = Abonnement::whereIn('id_entreprise', $entrepriseIds);
        $abonnementsTotal = (clone $abonnementsBase)->count();
        $abonnementsActifs = (clone $abonnementsBase)->where('status', 'actif')->count();
        $abonnementsExpires = (clone $abonnementsBase)
            ->where(function ($q) use ($today) {
                $q->where('status', 'expire')
                    ->orWhere('date_fin', '<', $today);
            })
            ->count();
        $abonnementsExpirentBientot = (clone $abonnementsBase)
            ->where('status', 'actif')
            ->whereBetween('date_fin', [$today, $today->copy()->addDays(30)])
            ->count();

        $commandesBase = Commande::query()
            ->whereHas('employe', fn ($q) => $q->whereIn('id_entreprise', $entrepriseIds));

        $impayesQuery = (clone $commandesBase)->where('statut_paiement', '!=', 'paye');

        $impayesCount = (clone $impayesQuery)->count();
        $impayesMontant = (float) (clone $impayesQuery)->sum('montant_total');

        $commandesTotalCount = (clone $commandesBase)->count();
        $commandesTotalMontant = (float) (clone $commandesBase)->sum('montant_total');

        $commandesAujourdHuiQuery = (clone $commandesBase)->whereDate('created_at', $today);
        $commandesAujourdHuiCount = (clone $commandesAujourdHuiQuery)->count();
        $commandesAujourdHuiMontant = (float) (clone $commandesAujourdHuiQuery)->sum('montant_total');

        $commandesMoisQuery = (clone $commandesBase)->whereBetween('created_at', [$debutMois, $finMois]);

        $commandesMoisCount = (clone $commandesMoisQuery)->count();
        $commandesMoisMontant = (float) (clone $commandesMoisQuery)->sum('montant_total');
        $commandesMoisPayeesCount = (clone $commandesMoisQuery)->where('statut_paiement', 'paye')->count();
        $commandesMoisImpayeesCount = (clone $commandesMoisQuery)->where('statut_paiement', '!=', 'paye')->count();
        $commandesMoisImpayeesMontant = (float) (clone $commandesMoisQuery)
            ->where('statut_paiement', '!=', 'paye')
            ->sum('montant_total');

        $commandesEnCoursCount = (clone $commandesBase)
            ->whereIn('statut_commande', ['en_attente', 'confirmee'])
            ->count();

        $paiementsMoisQuery = Paiement::query()
            ->whereHas('commande.employe', fn ($q) => $q->whereIn('id_entreprise', $entrepriseIds))
            ->whereBetween('created_at', [$debutMois, $finMois]);

        $paiementsMoisCount = (clone $paiementsMoisQuery)->count();
        $paiementsMoisMontant = (float) (clone $paiementsMoisQuery)->sum('montant');

        return [
            'entreprises_count' => $entreprisesCount,
            'entreprises_actives' => $entreprisesActives,
            'entreprises_inactives' => max(0, $entreprisesCount - $entreprisesActives),
            'employes_count' => $employesCount,
            'employes_actifs' => $employesActifs,
            'employes_inactifs' => max(0, $employesCount - $employesActifs),
            'boxes_count' => $boxesCount,
            'casiers_count' => $casiersCount,
            'casiers_libres' => $casiersLibres,
            'casiers_occupes' => $casiersOccupes,
            'casiers_hors_service' => $casiersHorsService,
            'taux_occupation_casiers' => $tauxOccupation,
            'abonnements_total' => $abonnementsTotal,
            'abonnements_actifs' => $abonnementsActifs,
            'abonnements_expires' => $abonnementsExpires,
            'abonnements_expirent_30j' => $abonnementsExpirentBientot,
            'commandes_total_count' => $commandesTotalCount,
            'commandes_total_montant' => $commandesTotalMontant,
            'commandes_aujourdhui_count' => $commandesAujourdHuiCount,
            'commandes_aujourdhui_montant' => $commandesAujourdHuiMontant,
            'commandes_mois_count' => $commandesMoisCount,
            'commandes_mois_montant' => $commandesMoisMontant,
            'commandes_mois_payees_count' => $commandesMoisPayeesCount,
            'commandes_mois_impayees_count' => $commandesMoisImpayeesCount,
            'commandes_mois_impayees_montant' => $commandesMoisImpayeesMontant,
            'commandes_en_cours_count' => $commandesEnCoursCount,
            'paiements_mois_count' => $paiementsMoisCount,
            'paiements_mois_montant' => $paiementsMoisMontant,
            'impayes_count' => $impayesCount,
            'impayes_montant' => $impayesMontant,
            'periode_mois' => $today->locale('fr')->translatedFormat('F Y'),
        ];
    }

    public function createBox(User $commercial, array $validated): Box
    {
        $entreprise = $this->findEntrepriseForCommercial((int) $validated['id_entreprise'], $commercial);

        $box = Box::create([
            'ref' => Box::generateRef(),
            'nom' => $validated['nom'],
            'id_entreprise' => $entreprise->id,
            'adresse' => $validated['adresse'] ?? null,
            'lat' => $validated['lat'] ?? null,
            'long' => $validated['long'] ?? null,
            'capacite' => (int) $validated['capacite'],
            'est_actif' => true,
        ]);

        $this->genererCasiers($box, (int) $validated['capacite']);

        return $box->load(['entreprise', 'casiers']);
    }

    public function genererCasiers(Box $box, int $nombre, int $numeroDebut = 1): void
    {
        for ($i = 0; $i < $nombre; $i++) {
            $numero = $numeroDebut + $i;
            Casier::create([
                'ref' => Casier::generateRef($box->ref, $numero),
                'qr_code' => Casier::generateQRCode(),
                'id_box' => $box->id,
                'numero_casier' => $numero,
                'statut' => 'libre',
            ]);
        }
    }

    public function assignCasier(User $commercial, Box $box, Casier $casier, int $employeId): Casier
    {
        if ((int) $casier->id_box !== (int) $box->id) {
            throw ValidationException::withMessages(['casier' => 'Casier invalide pour cette box.']);
        }

        if ($casier->statut === 'hors_service') {
            throw ValidationException::withMessages(['casier' => 'Ce casier est hors service.']);
        }

        $employe = User::findOrFail($employeId);
        if ($employe->role !== 'employe') {
            throw ValidationException::withMessages(['employe_id' => 'Utilisateur invalide (employé requis).']);
        }

        $this->findEntrepriseForCommercial((int) $employe->id_entreprise, $commercial);

        if ((int) $employe->id_entreprise !== (int) $box->id_entreprise) {
            throw ValidationException::withMessages(['employe_id' => 'Cet employé n’appartient pas à l’entreprise de la box.']);
        }

        $casier->id_employe = $employe->id;
        $casier->statut = 'occupe';
        $casier->save();

        $employe->update(['num_box' => $casier->ref]);

        return $casier->fresh(['employe']);
    }

    public function unassignCasier(Casier $casier): Casier
    {
        if ($casier->id_employe) {
            User::where('id', $casier->id_employe)->update(['num_box' => null]);
        }
        $casier->id_employe = null;
        if ($casier->statut === 'occupe') {
            $casier->statut = 'libre';
        }
        $casier->save();

        return $casier->fresh();
    }

    public function employesForEntreprise(Entreprise $entreprise)
    {
        return User::query()
            ->where('role', 'employe')
            ->where('id_entreprise', $entreprise->id)
            ->orderBy('name')
            ->get()
            ->map(function (User $u) {
                $casier = Casier::where('id_employe', $u->id)->first();

                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'telephone' => $u->telephone,
                    'num_box' => $u->num_box,
                    'is_active' => (bool) $u->is_active,
                    'casier' => $casier ? [
                        'id' => $casier->id,
                        'ref' => $casier->ref,
                        'numero_casier' => $casier->numero_casier,
                        'statut' => $casier->statut,
                        'box_id' => $casier->id_box,
                    ] : null,
                ];
            });
    }

    public function abonnementsQuery(User $commercial, ?int $entrepriseId = null): Builder
    {
        $q = Abonnement::query()
            ->with('entreprise')
            ->whereIn('id_entreprise', $this->entrepriseIdsForCommercial($commercial));

        if ($entrepriseId) {
            $this->findEntrepriseForCommercial($entrepriseId, $commercial);
            $q->where('id_entreprise', $entrepriseId);
        }

        return $q->orderByDesc('created_at');
    }

    public function paiementsQuery(User $commercial, ?int $entrepriseId = null): Builder
    {
        $entrepriseIds = $this->entrepriseIdsForCommercial($commercial);

        if ($entrepriseId) {
            $this->findEntrepriseForCommercial($entrepriseId, $commercial);
            $entrepriseIds = collect([$entrepriseId]);
        }

        return Paiement::query()
            ->with(['commande.employe.entreprise', 'user'])
            ->whereHas('commande.employe', fn ($q) => $q->whereIn('id_entreprise', $entrepriseIds))
            ->orderByDesc('created_at');
    }

    public function impayesQuery(User $commercial, ?int $entrepriseId = null): Builder
    {
        $entrepriseIds = $this->entrepriseIdsForCommercial($commercial);

        if ($entrepriseId) {
            $this->findEntrepriseForCommercial($entrepriseId, $commercial);
            $entrepriseIds = collect([$entrepriseId]);
        }

        return Commande::query()
            ->with(['employe.entreprise', 'paiements'])
            ->whereHas('employe', fn ($q) => $q->whereIn('id_entreprise', $entrepriseIds))
            ->where('statut_paiement', '!=', 'paye')
            ->orderByDesc('created_at');
    }

    public function entrepriseToArray(Entreprise $e): array
    {
        return [
            'id' => $e->id,
            'nom' => $e->nom,
            'adresse' => $e->adresse,
            'ville' => $e->ville,
            'pays' => $e->pays,
            'numero' => $e->numero,
            'lat' => $e->lat,
            'long' => $e->long,
            'commune_id' => $e->commune_id,
            'statut' => (bool) $e->statut,
            'employes_count' => $e->employes()->count(),
            'boxes_count' => $e->boxes()->count(),
            'logo' => $e->logo,
            'created_at' => optional($e->created_at)->toISOString(),
        ];
    }

    public function validateEmployePayload(Request $request, ?User $employe = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($employe?->id)],
            'telephone' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ];

        if (! $employe) {
            $rules['password'] = 'required|string|min:8';
        } elseif ($request->filled('password')) {
            $rules['password'] = 'string|min:8';
        }

        return $request->validate($rules);
    }

    public function findEmployeForCommercial(int $employeId, User $commercial): User
    {
        $employe = User::query()
            ->where('role', 'employe')
            ->findOrFail($employeId);

        $this->findEntrepriseForCommercial((int) $employe->id_entreprise, $commercial);

        return $employe;
    }

    public function createEmploye(User $commercial, Entreprise $entreprise, array $validated, Request $request): User
    {
        return User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'telephone' => $validated['telephone'] ?? null,
            'role' => 'employe',
            'id_entreprise' => $entreprise->id,
            'is_active' => array_key_exists('is_active', $validated)
                ? (bool) $validated['is_active']
                : ($request->has('is_active') ? $request->boolean('is_active') : true),
        ]);
    }

    public function updateEmploye(User $employe, array $validated, Request $request): User
    {
        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'] ?? null,
        ];

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        } elseif (array_key_exists('is_active', $validated)) {
            $data['is_active'] = (bool) $validated['is_active'];
        }

        if (! empty($validated['password'] ?? null)) {
            $data['password'] = Hash::make($validated['password']);
        }

        $employe->update($data);

        return $employe->fresh();
    }

    public function employeToArray(User $u): array
    {
        $casier = Casier::where('id_employe', $u->id)->first();

        return [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'telephone' => $u->telephone,
            'num_box' => $u->num_box,
            'is_active' => (bool) $u->is_active,
            'id_entreprise' => $u->id_entreprise,
            'casier' => $casier ? [
                'id' => $casier->id,
                'ref' => $casier->ref,
                'numero_casier' => $casier->numero_casier,
                'statut' => $casier->statut,
                'box_id' => $casier->id_box,
            ] : null,
        ];
    }
}
