<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Afficher la liste des utilisateurs
     */
    public function index(Request $request)
    {
        $query = User::query()->orderBy('created_at', 'desc');

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('is_active')) {
            $isActive = $request->input('is_active');
            if ($isActive === '1' || $isActive === 1 || $isActive === true || $isActive === 'true') {
                $query->where('is_active', true);
            } elseif ($isActive === '0' || $isActive === 0 || $isActive === false || $isActive === 'false') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('telephone', 'like', "%{$q}%");
            });
        }

        $users = $query->paginate(10)->appends($request->only(['role', 'is_active', 'q']));
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Enregistrer un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'telephone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,entreprise,livreur,employe,commercial',
            'id_entreprise' => 'nullable|integer',
            'num_box' => 'nullable|string|max:50',
        ], [
            'name.required' => 'Le nom est requis.',
            'email.required' => 'L\'email est requis.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'role.required' => 'Le rôle est requis.',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');

        User::create($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès !');
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'telephone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,entreprise,livreur,employe,commercial',
            'id_entreprise' => 'nullable|integer',
            'num_box' => 'nullable|string|max:50',
        ], [
            'name.required' => 'Le nom est requis.',
            'email.required' => 'L\'email est requis.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'role.required' => 'Le rôle est requis.',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès !');
    }

    /**
     * Afficher le formulaire de changement de mot de passe
     */
    public function changePassword($id)
    {
        $user = User::findOrFail($id);
        
        return view('admin.users.change-password', compact('user'));
    }

    /**
     * Mettre à jour le mot de passe
     */
    public function updatePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Mot de passe modifié avec succès !');
    }

    /**
     * Activer/Désactiver un utilisateur
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        // Ne pas permettre de désactiver son propre compte
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Vous ne pouvez pas désactiver votre propre compte !');
        }

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $status = $user->is_active ? 'activé' : 'désactivé';

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Compte {$status} avec succès !");
    }
}
