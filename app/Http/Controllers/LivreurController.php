<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Entreprise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LivreurController extends Controller
{
    /**
     * Display a listing of livreurs.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'livreur')->with(['entreprise', 'entreprises']);

        // Filtres
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('is_active', $request->status == 'active' ? 1 : 0);
        }

        if ($request->has('entreprise') && $request->entreprise != '') {
            $query->whereHas('entreprises', function($q) use ($request) {
                $q->where('entreprises.id', $request->entreprise);
            });
        }

        $livreurs = $query->orderBy('created_at', 'desc')->paginate(10);
        $entreprises = Entreprise::where('statut', true)->orderBy('nom')->get();

        $totalLivreurs = User::where('role', 'livreur')->count();
        $livreursActifs = User::where('role', 'livreur')->where('is_active', true)->count();
        $livreursInactifs = User::where('role', 'livreur')->where('is_active', false)->count();

        return view('admin.livreurs.index', compact('livreurs', 'entreprises', 'totalLivreurs', 'livreursActifs', 'livreursInactifs'));
    }

    /**
     * Show the form for creating a new livreur.
     */
    public function create()
    {
        $entreprises = Entreprise::where('statut', true)->orderBy('nom')->get();
        return view('admin.livreurs.create', compact('entreprises'));
    }

    /**
     * Store a newly created livreur.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'required|string|max:20',
            'password' => 'required|string|min:6',
            'id_entreprise' => 'nullable|exists:entreprises,id',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'],
            'password' => Hash::make($validated['password']),
            'role' => 'livreur',
            'id_entreprise' => $validated['id_entreprise'] ?? null,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.livreurs.index')
            ->with('success', 'Livreur créé avec succès.');
    }

    /**
     * Display the specified livreur.
     */
    public function show($id)
    {
        $livreur = User::where('role', 'livreur')->with(['entreprise', 'entreprises'])->findOrFail($id);
        
        // Statistiques du livreur (à implémenter avec les commandes plus tard)
        $stats = [
            'livraisons_total' => 0,
            'livraisons_mois' => 0,
            'livraisons_jour' => 0,
            'en_cours' => 0,
        ];

        $entreprisesDisponibles = Entreprise::where('statut', true)->orderBy('nom')->get();

        return view('admin.livreurs.show', compact('livreur', 'stats', 'entreprisesDisponibles'));
    }

    /**
     * Show the form for editing the specified livreur.
     */
    public function edit($id)
    {
        $livreur = User::where('role', 'livreur')->findOrFail($id);
        $entreprises = Entreprise::where('statut', true)->orderBy('nom')->get();
        
        return view('admin.livreurs.edit', compact('livreur', 'entreprises'));
    }

    /**
     * Update the specified livreur.
     */
    public function update(Request $request, $id)
    {
        $livreur = User::where('role', 'livreur')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'telephone' => 'required|string|max:20',
            'id_entreprise' => 'nullable|exists:entreprises,id',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'telephone.required' => 'Le téléphone est obligatoire.',
        ]);

        $livreur->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'],
            'id_entreprise' => $validated['id_entreprise'] ?? null,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.livreurs.index')
            ->with('success', 'Livreur modifié avec succès.');
    }

    /**
     * Toggle livreur status.
     */
    public function toggleStatus($id)
    {
        $livreur = User::where('role', 'livreur')->findOrFail($id);
        
        $livreur->update([
            'is_active' => !$livreur->is_active
        ]);

        return redirect()->back()
            ->with('success', 'Statut du livreur mis à jour.');
    }

    /**
     * Affecter plusieurs entreprises au livreur.
     */
    public function affectEntreprises(Request $request, $id)
    {
        $livreur = User::where('role', 'livreur')->findOrFail($id);

        $validated = $request->validate([
            'entreprises' => 'required|array|min:1',
            'entreprises.*' => 'exists:entreprises,id',
        ], [
            'entreprises.required' => 'Veuillez sélectionner au moins une entreprise.',
            'entreprises.min' => 'Veuillez sélectionner au moins une entreprise.',
            'entreprises.*.exists' => 'Une des entreprises sélectionnées n\'existe pas.',
        ]);

        // Sync les entreprises (remplace toutes les affectations existantes)
        $livreur->entreprises()->sync($validated['entreprises']);

        $count = count($validated['entreprises']);
        return redirect()->back()
            ->with('success', $count . ' entreprise(s) affectée(s) avec succès.');
    }

    /**
     * Retirer une entreprise spécifique du livreur.
     */
    public function removeEntreprise(Request $request, $id)
    {
        $livreur = User::where('role', 'livreur')->findOrFail($id);
        
        $validated = $request->validate([
            'entreprise_id' => 'required|exists:entreprises,id',
        ]);

        $livreur->entreprises()->detach($validated['entreprise_id']);

        return redirect()->back()
            ->with('success', 'Entreprise retirée avec succès.');
    }

    /**
     * Retirer toutes les entreprises du livreur.
     */
    public function removeAllEntreprises($id)
    {
        $livreur = User::where('role', 'livreur')->findOrFail($id);
        
        $livreur->entreprises()->detach();

        return redirect()->back()
            ->with('success', 'Toutes les entreprises ont été retirées.');
    }

    /**
     * Change livreur password.
     */
    public function changePassword($id)
    {
        $livreur = User::where('role', 'livreur')->findOrFail($id);
        return view('admin.livreurs.change-password', compact('livreur'));
    }

    /**
     * Update livreur password.
     */
    public function updatePassword(Request $request, $id)
    {
        $livreur = User::where('role', 'livreur')->findOrFail($id);

        $validated = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        $livreur->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()->route('admin.livreurs.index')
            ->with('success', 'Mot de passe modifié avec succès.');
    }
}
