<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\Casier;
use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Http\Request;

class BoxController extends Controller
{
    /**
     * Afficher la liste des boxes
     */
    public function index()
    {
        $boxes = Box::with(['entreprise', 'casiers'])->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.boxes.index', compact('boxes'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $entreprises = Entreprise::where('statut', true)->orderBy('nom')->get();
        
        return view('admin.boxes.create', compact('entreprises'));
    }

    /**
     * Enregistrer une nouvelle box
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'id_entreprise' => 'required|exists:entreprises,id',
            'adresse' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',
            'capacite' => 'required|integer|min:1|max:200',
        ]);

        $validated['ref'] = Box::generateRef();

        $box = Box::create($validated);

        // Générer les casiers
        $this->genererCasiers($box, $validated['capacite']);

        return redirect()->route('admin.boxes.index')
            ->with('success', "Box créée avec succès ! {$validated['capacite']} casiers générés.");
    }

    /**
     * Afficher les détails d'une box
     */
    public function show(Box $box)
    {
        $box->load(['entreprise', 'casiers.employe']);
        
        return view('admin.boxes.show', compact('box'));
    }

    /**
     * Liste des casiers d'une box (JSON) pour sélection / impression QR.
     */
    public function listCasiers(Request $request, Box $box)
    {
        $q = trim((string) $request->input('q', ''));

        $casiers = $box->casiers()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('numero_casier', 'like', "%{$q}%")
                        ->orWhere('ref', 'like', "%{$q}%")
                        ->orWhere('qr_code', 'like', "%{$q}%");
                });
            })
            ->orderBy('numero_casier')
            ->limit(300)
            ->get(['id', 'numero_casier', 'ref', 'qr_code', 'statut', 'id_employe']);

        return response()->json([
            'success' => true,
            'data' => $casiers,
        ]);
    }

    /**
     * Page imprimable des QR codes (tous ou sélection).
     * Query params:
     * - casier_ids: "1,2,3" (optionnel)
     */
    public function printCasiersQRCodes(Request $request, Box $box)
    {
        $idsRaw = trim((string) $request->input('casier_ids', ''));
        $ids = [];
        if ($idsRaw !== '') {
            $ids = collect(explode(',', $idsRaw))
                ->map(fn ($v) => (int) trim($v))
                ->filter(fn ($v) => $v > 0)
                ->unique()
                ->values()
                ->all();
        }

        $query = $box->casiers()->orderBy('numero_casier');
        if (!empty($ids)) {
            $query->whereIn('id', $ids);
        }

        $casiers = $query->get(['id', 'numero_casier', 'ref', 'qr_code']);
        $box->loadMissing('entreprise');

        return view('admin.boxes.print_qr', [
            'box' => $box,
            'casiers' => $casiers,
            'selectedCount' => count($ids) > 0 ? count($ids) : null,
        ]);
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit(Box $box)
    {
        $entreprises = Entreprise::where('statut', true)->orderBy('nom')->get();
        
        return view('admin.boxes.edit', compact('box', 'entreprises'));
    }

    /**
     * Mettre à jour une box
     */
    public function update(Request $request, Box $box)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'id_entreprise' => 'required|exists:entreprises,id',
            'adresse' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',
        ]);

        $box->update($validated);

        return redirect()->route('admin.boxes.index')
            ->with('success', 'Box mise à jour avec succès !');
    }

    /**
     * Supprimer une box
     */
    public function destroy(Box $box)
    {
        $box->delete();

        return redirect()->route('admin.boxes.index')
            ->with('success', 'Box supprimée avec succès !');
    }

    /**
     * Activer/Désactiver une box
     */
    public function toggleStatus(Box $box)
    {
        $box->update(['est_actif' => !$box->est_actif]);

        $status = $box->est_actif ? 'activée' : 'désactivée';

        return redirect()->back()
            ->with('success', "Box {$status} avec succès !");
    }

    /**
     * Générer des casiers supplémentaires
     */
    public function ajouterCasiers(Request $request, Box $box)
    {
        $validated = $request->validate([
            'nombre' => 'required|integer|min:1|max:50',
        ]);

        $dernierNumero = $box->casiers()->max('numero_casier') ?? 0;
        
        $this->genererCasiers($box, $validated['nombre'], $dernierNumero + 1);

        $box->update(['capacite' => $box->capacite + $validated['nombre']]);

        return redirect()->back()
            ->with('success', "{$validated['nombre']} casiers ajoutés avec succès !");
    }

    /**
     * Générer les casiers pour une box
     */
    private function genererCasiers(Box $box, $nombre, $numeroDebut = 1)
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

    /**
     * Recherche employés (pour attribution casier).
     * Filtre par entreprise de la box et par query (nom / email / téléphone).
     */
    public function searchEmployes(Request $request, Box $box)
    {
        $q = trim((string) $request->input('q', ''));

        $query = User::query()
            ->where('role', 'employe')
            ->where('is_active', true)
            ->where('id_entreprise', $box->id_entreprise);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('telephone', 'like', "%{$q}%");
            });
        }

        $users = $query->orderBy('name')->limit(15)->get(['id', 'name', 'email', 'telephone', 'num_box']);

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    /**
     * Attribuer un employé à un casier.
     */
    public function assignEmploye(Request $request, Box $box, Casier $casier)
    {
        if ((int) $casier->id_box !== (int) $box->id) {
            abort(404);
        }

        if ($casier->statut === 'hors_service') {
            return redirect()->back()->with('error', 'Ce casier est hors service : attribution impossible.');
        }

        $validated = $request->validate([
            'employe_id' => 'required|exists:users,id',
        ]);

        $employe = User::findOrFail($validated['employe_id']);
        if ($employe->role !== 'employe') {
            return redirect()->back()->with('error', 'Utilisateur invalide (doit être un employé).');
        }
        if ((int) $employe->id_entreprise !== (int) $box->id_entreprise) {
            return redirect()->back()->with('error', 'Cet employé n’appartient pas à l’entreprise de cette box.');
        }

        $casier->id_employe = $employe->id;
        // Règle: dès qu’on attribue un employé → OCCUPÉ
        $casier->statut = 'occupe';
        $casier->save();

        return redirect()->back()->with('success', "Employé attribué au casier {$casier->ref}.");
    }

    /**
     * Désattribuer l'employé d'un casier.
     */
    public function unassignEmploye(Request $request, Box $box, Casier $casier)
    {
        if ((int) $casier->id_box !== (int) $box->id) {
            abort(404);
        }

        $casier->id_employe = null;
        // Si on retire l’employé d’un casier occupé → libre (sauf hors service).
        if ($casier->statut === 'occupe') {
            $casier->statut = 'libre';
        }
        $casier->save();

        return redirect()->back()->with('success', "Employé retiré du casier {$casier->ref}.");
    }

    /**
     * Changer le statut d’un casier (libre/occupe/reserve/hors_service).
     * Règles:
     * - hors_service: on ne peut pas attribuer
     * - si statut=occupe alors id_employe doit exister (sinon on refuse)
     */
    public function updateCasierStatus(Request $request, Box $box, Casier $casier)
    {
        if ((int) $casier->id_box !== (int) $box->id) {
            abort(404);
        }

        $validated = $request->validate([
            'statut' => 'required|in:libre,occupe,reserve,hors_service',
        ]);

        $new = $validated['statut'];

        if ($new === 'occupe' && !$casier->id_employe) {
            return redirect()->back()->with('error', 'Impossible de passer en occupé sans employé attribué.');
        }

        if ($new !== 'occupe' && $casier->id_employe && $new === 'libre') {
            // Option: garder l’employé même si on met libre ? On choisit cohérence: libre = pas d’employé.
            $casier->id_employe = null;
        }

        $casier->statut = $new;
        $casier->save();

        return redirect()->back()->with('success', "Statut du casier {$casier->ref} mis à jour.");
    }
}
