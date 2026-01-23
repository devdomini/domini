<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\Casier;
use App\Models\Entreprise;
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
}
