@extends('admin.layout')

@section('title', 'Ajouter une entreprise')
@section('page-title', 'Ajouter une entreprise')

@section('content')
    <div style="max-width: 900px;">
        <!-- Back Button -->
        <div style="margin-bottom: 2rem;">
            <a href="{{ route('admin.entreprises.index') }}" style="color: #FF0000; text-decoration: none; font-weight: 600;">
                ← Retour à la liste
            </a>
        </div>

        <!-- Form Card -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Informations de l'entreprise</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.entreprises.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div style="display: grid; gap: 1.5rem;">
                        <!-- Nom -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                                Nom de l'entreprise <span style="color: #FF0000;">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="nom" 
                                value="{{ old('nom') }}"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                placeholder="Ex: Orange Côte d'Ivoire"
                                required
                            >
                            @error('nom')
                                <span style="color: #CC0000; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Adresse -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                                Adresse complète <span style="color: #FF0000;">*</span>
                            </label>
                            <textarea 
                                name="adresse" 
                                rows="3"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem; font-family: 'Inter', sans-serif;"
                                placeholder="Ex: Plateau, Rue des Jardins, Immeuble"
                                required
                            >{{ old('adresse') }}</textarea>
                            @error('adresse')
                                <span style="color: #CC0000; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Ville et Pays -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div>
                                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                                    Ville <span style="color: #FF0000;">*</span>
                                </label>
                                <select 
                                    name="ville" 
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                    required
                                >
                                    <option value="">Sélectionner une ville</option>
                                    <option value="Abidjan" {{ old('ville') == 'Abidjan' ? 'selected' : '' }}>Abidjan</option>
                                    <option value="Bouaké" {{ old('ville') == 'Bouaké' ? 'selected' : '' }}>Bouaké</option>
                                    <option value="Yamoussoukro" {{ old('ville') == 'Yamoussoukro' ? 'selected' : '' }}>Yamoussoukro</option>
                                    <option value="Daloa" {{ old('ville') == 'Daloa' ? 'selected' : '' }}>Daloa</option>
                                    <option value="Korhogo" {{ old('ville') == 'Korhogo' ? 'selected' : '' }}>Korhogo</option>
                                    <option value="San-Pédro" {{ old('ville') == 'San-Pédro' ? 'selected' : '' }}>San-Pédro</option>
                                </select>
                                @error('ville')
                                    <span style="color: #CC0000; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                                    Pays
                                </label>
                                <input 
                                    type="text" 
                                    name="pays" 
                                    value="{{ old('pays', 'Côte d\'Ivoire') }}"
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                >
                                @error('pays')
                                    <span style="color: #CC0000; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Numéro -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                                Numéro de téléphone
                            </label>
                            <input 
                                type="tel" 
                                name="numero" 
                                value="{{ old('numero') }}"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                placeholder="Ex: +225 27 20 00 00 00"
                            >
                            @error('numero')
                                <span style="color: #CC0000; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Coordonnées GPS -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div>
                                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                                    Latitude
                                </label>
                                <input 
                                    type="text" 
                                    inputmode="decimal"
                                    name="lat" 
                                    value="{{ old('lat') }}"
                                    placeholder="ex. 5.3546081"
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                    placeholder="Ex: 5.316667"
                                >
                                @error('lat')
                                    <span style="color: #CC0000; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                                    Longitude
                                </label>
                                <input 
                                    type="text" 
                                    inputmode="decimal"
                                    name="long" 
                                    value="{{ old('long') }}"
                                    placeholder="ex. -3.9814236"
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                    placeholder="Ex: -4.033333"
                                >
                                @error('long')
                                    <span style="color: #CC0000; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <small style="color: #666; font-size: 0.75rem; margin-top: -1rem;">Les coordonnées GPS sont optionnelles mais utiles pour la géolocalisation</small>

                        <!-- Commercial responsable -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                                Commercial responsable
                            </label>
                            <select
                                name="commercial_id"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                            >
                                <option value="">— Non assigné —</option>
                                @foreach($commerciaux as $commercial)
                                    <option value="{{ $commercial->id }}" {{ old('commercial_id') == $commercial->id ? 'selected' : '' }}>
                                        {{ $commercial->name }} ({{ $commercial->email }})
                                    </option>
                                @endforeach
                            </select>
                            <small style="color: #666; font-size: 0.75rem;">Permet au commercial de voir l'entreprise, ses employés et leurs messages.</small>
                            @error('commercial_id')
                                <span style="color: #CC0000; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Commune (liée à un entrepôt) -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                                Commune (trajets / livraison entreprise)
                            </label>
                            <select
                                name="commune_id"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                            >
                                <option value="">— Aucune —</option>
                                @foreach($communes as $c)
                                    <option value="{{ $c->id }}" {{ old('commune_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->nom }} @if($c->warehouse) ({{ $c->warehouse->name }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            <small style="color: #666; font-size: 0.75rem;">Créez les communes sous <strong>Communes</strong> si la liste est vide.</small>
                            @error('commune_id')
                                <span style="color: #CC0000; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Logo -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                                Logo de l'entreprise
                            </label>
                            <input 
                                type="file" 
                                name="logo" 
                                accept="image/*"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                            >
                            <small style="color: #666; font-size: 0.75rem;">Format accepté: JPG, PNG (Max 2MB)</small>
                            @error('logo')
                                <span style="color: #CC0000; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Statut -->
                        <div>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="statut" value="1" checked style="width: 18px; height: 18px;">
                                <span style="font-weight: 600; color: #000000;">Entreprise active</span>
                            </label>
                            <small style="color: #666; font-size: 0.75rem;">Une entreprise active peut recevoir des commandes</small>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #E5E5E5;">
                        <button type="submit" class="btn btn-primary">
                            Créer l'entreprise
                        </button>
                        <a href="{{ route('admin.entreprises.index') }}" class="btn btn-secondary">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
