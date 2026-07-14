@extends('admin.layout')

@section('title', 'Ajouter un Livreur')
@section('page-title', 'Ajouter un Livreur')

@section('content')
    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div style="padding: 2rem;">
            <div style="margin-bottom: 2rem;">
                <a href="{{ route('admin.livreurs.index') }}" style="color: #FF0000; text-decoration: none; font-weight: 600;">
                    ← Retour à la liste
                </a>
            </div>

            <form action="{{ route('admin.livreurs.store') }}" method="POST">
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
                            required
                            style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                            placeholder="Ex: Jean Kouassi"
                        >
                        @error('name')
                            <small style="color: #EF4444;">{{ $message }}</small>
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
                            required
                            style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                            placeholder="livreur@domini.com"
                        >
                        @error('email')
                            <small style="color: #EF4444;">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Téléphone -->
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                            Téléphone <span style="color: #FF0000;">*</span>
                        </label>
                        <input 
                            type="tel" 
                            name="telephone" 
                            value="{{ old('telephone') }}"
                            required
                            style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                            placeholder="+225 XX XX XX XX XX"
                        >
                        @error('telephone')
                            <small style="color: #EF4444;">{{ $message }}</small>
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
                            required
                            style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                            placeholder="Minimum 6 caractères"
                        >
                        @error('password')
                            <small style="color: #EF4444;">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Entrepôt -->
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                            Entrepôt (optionnel)
                        </label>
                        <select 
                            name="warehouse_id" 
                            style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                        >
                            <option value="">— Aucun entrepôt —</option>
                            @foreach($warehouses as $w)
                                <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }} ({{ $w->city }})</option>
                            @endforeach
                        </select>
                        <small style="color: #666; font-size: 0.75rem;">Entrepôt de rattachement pour les trajets et livraisons.</small>
                        @error('warehouse_id')
                            <small style="color: #EF4444;">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Type livreur -->
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                            Type livreur
                        </label>
                        <select
                            name="type_livreur"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                        >
                            <option value="">— Non défini —</option>
                            <option value="classique" {{ old('type_livreur') == 'classique' ? 'selected' : '' }}>Classique</option>
                            <option value="entreprise" {{ old('type_livreur') == 'entreprise' ? 'selected' : '' }}>Entreprise (lot)</option>
                        </select>
                        <small style="color: #666; font-size: 0.75rem;">Permet de filtrer et d’éviter les affectations incompatibles.</small>
                        @error('type_livreur')
                            <small style="color: #EF4444;">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Entreprise -->
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                            Entreprise (optionnel)
                        </label>
                        <select 
                            name="id_entreprise" 
                            style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                        >
                            <option value="">Aucune (à affecter plus tard)</option>
                            @foreach($entreprises as $entreprise)
                                <option value="{{ $entreprise->id }}" {{ old('id_entreprise') == $entreprise->id ? 'selected' : '' }}>
                                    {{ $entreprise->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_entreprise')
                            <small style="color: #EF4444;">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Actif -->
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                            <span style="font-weight: 600; color: #000000;">Livreur actif</span>
                        </label>
                    </div>
                </div>

                <!-- Buttons -->
                <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #E5E5E5;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        Créer le livreur
                    </button>
                    <a href="{{ route('admin.livreurs.index') }}" class="btn btn-secondary">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
