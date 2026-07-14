@extends('admin.layout')

@section('title', 'Ajouter un utilisateur')
@section('page-title', 'Ajouter un utilisateur')

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
                <h2 class="card-title">Informations de l'utilisateur</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf

                    <div style="display: grid; gap: 1.5rem;">
                        <!-- Nom -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                                Nom complet <span style="color: #FF0000;">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="name" 
                                value="{{ old('name') }}"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                placeholder="Ex: Jean Kouassi"
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
                                value="{{ old('email') }}"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                placeholder="Ex: jean.kouassi@example.com"
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
                                value="{{ old('telephone') }}"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                placeholder="Ex: +225 0123456789"
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
                                <option value="">Sélectionner un rôle</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrateur</option>
                                <option value="entreprise" {{ old('role') == 'entreprise' ? 'selected' : '' }}>Entreprise</option>
                                <option value="livreur" {{ old('role') == 'livreur' ? 'selected' : '' }}>Livreur</option>
                                <option value="employe" {{ old('role') == 'employe' ? 'selected' : '' }}>Employé</option>
                                <option value="commercial" {{ old('role') == 'commercial' ? 'selected' : '' }}>Commercial</option>
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
                                value="{{ old('id_entreprise') }}"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                placeholder="Ex: 1"
                            >
                            <small style="color: #666; font-size: 0.75rem;">Requis pour les employés</small>
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
                                value="{{ old('num_box') }}"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                placeholder="Ex: A-12"
                            >
                            <small style="color: #666; font-size: 0.75rem;">Requis pour les employés</small>
                            @error('num_box')
                                <span style="color: #CC0000; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Mot de passe -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                                Mot de passe <span style="color: #FF0000;">*</span>
                            </label>
                            <input 
                                type="password" 
                                name="password" 
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                placeholder="Min. 8 caractères"
                                required
                            >
                            @error('password')
                                <span style="color: #CC0000; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Confirmation mot de passe -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                                Confirmer le mot de passe <span style="color: #FF0000;">*</span>
                            </label>
                            <input 
                                type="password" 
                                name="password_confirmation" 
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                placeholder="Retapez le mot de passe"
                                required
                            >
                        </div>

                        <!-- Statut -->
                        <div>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="is_active" value="1" checked style="width: 18px; height: 18px;">
                                <span style="font-weight: 600; color: #000000;">Compte actif</span>
                            </label>
                            <small style="color: #666; font-size: 0.75rem;">Si décoché, l'utilisateur ne pourra pas se connecter</small>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #E5E5E5;">
                        <button type="submit" class="btn btn-primary">
                            Créer l'utilisateur
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
