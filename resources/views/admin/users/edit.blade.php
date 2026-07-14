@extends('admin.layout')

@section('title', 'Modifier un utilisateur')
@section('page-title', 'Modifier l\'utilisateur')

@section('content')
    <div style="max-width: 800px;">
        <!-- Back Button -->
        <div style="margin-bottom: 2rem;">
            <a href="{{ route('admin.users.index') }}" style="color: #FF0000; text-decoration: none; font-weight: 600;">
                ← Retour à la liste
            </a>
        </div>

        <!-- Form Card -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Modifier les informations</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.update', $user->id ?? 0) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div style="display: grid; gap: 1.5rem;">
                        <!-- Nom -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                                Nom complet <span style="color: #FF0000;">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="name" 
                                value="{{ old('name', $user->name ?? '') }}"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                required
                            >
                            @error('name')
                                <span style="color: #CC0000; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                                Email <span style="color: #FF0000;">*</span>
                            </label>
                            <input 
                                type="email" 
                                name="email" 
                                value="{{ old('email', $user->email ?? '') }}"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                required
                            >
                            @error('email')
                                <span style="color: #CC0000; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Téléphone -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                                Téléphone
                            </label>
                            <input 
                                type="tel" 
                                name="telephone" 
                                value="{{ old('telephone', $user->telephone ?? '') }}"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                            >
                            @error('telephone')
                                <span style="color: #CC0000; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Rôle -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                                Rôle <span style="color: #FF0000;">*</span>
                            </label>
                            <select 
                                name="role" 
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                required
                            >
                                <option value="admin" {{ old('role', $user->role ?? '') == 'admin' ? 'selected' : '' }}>Administrateur</option>
                                <option value="entreprise" {{ old('role', $user->role ?? '') == 'entreprise' ? 'selected' : '' }}>Entreprise</option>
                                <option value="livreur" {{ old('role', $user->role ?? '') == 'livreur' ? 'selected' : '' }}>Livreur</option>
                                <option value="employe" {{ old('role', $user->role ?? '') == 'employe' ? 'selected' : '' }}>Employé</option>
                                <option value="commercial" {{ old('role', $user->role ?? '') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                            </select>
                            @error('role')
                                <span style="color: #CC0000; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- ID Entreprise -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                                ID Entreprise
                            </label>
                            <input 
                                type="number" 
                                name="id_entreprise" 
                                value="{{ old('id_entreprise', $user->id_entreprise ?? '') }}"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                            >
                            @error('id_entreprise')
                                <span style="color: #CC0000; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Numéro de Box -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                                Numéro de casier
                            </label>
                            <input 
                                type="text" 
                                name="num_box" 
                                value="{{ old('num_box', $user->num_box ?? '') }}"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                            >
                            @error('num_box')
                                <span style="color: #CC0000; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Statut -->
                        <div>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input 
                                    type="checkbox" 
                                    name="is_active" 
                                    value="1" 
                                    {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}
                                    style="width: 18px; height: 18px;"
                                >
                                <span style="font-weight: 600; color: #000000;">Compte actif</span>
                            </label>
                            <small style="color: #666; font-size: 0.75rem;">Si décoché, l'utilisateur ne pourra pas se connecter</small>
                        </div>

                        <!-- Info -->
                        <div style="background-color: #FFF9E6; border-left: 4px solid #CC0000; padding: 1rem; border-radius: 4px;">
                            <p style="color: #000000; font-size: 0.875rem; margin: 0;">
                                <strong>Note:</strong> Pour modifier le mot de passe, utilisez le bouton "Modifier le mot de passe" dans la liste des utilisateurs.
                            </p>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #E5E5E5;">
                        <button type="submit" class="btn btn-primary">
                            Enregistrer les modifications
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
