@extends('admin.layout')

@section('title', 'Modifier une entreprise')
@section('page-title', 'Modifier l\'entreprise')

@section('content')
    <div style="max-width: 900px;">
        <!-- Back Button -->
        <div style="margin-bottom: 2rem;">
            <a href="{{ route('admin.entreprises.index') }}" style="color: #D9542A; text-decoration: none; font-weight: 600;">
                ← Retour à la liste
            </a>
        </div>

        <!-- Form Card -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Modifier les informations</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.entreprises.update', $entreprise->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div style="display: grid; gap: 1.5rem;">
                        <!-- Logo actuel -->
                        @if($entreprise->logo)
                        <div style="text-align: center; padding: 1rem; background-color: #FDFBF8; border-radius: 8px;">
                            <p style="font-weight: 600; color: #3A3A3A; margin-bottom: 0.5rem;">Logo actuel</p>
                            <img src="{{ asset('storage/' . $entreprise->logo) }}" alt="{{ $entreprise->nom }}" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #D9542A;">
                        </div>
                        @endif

                        <!-- Nom -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #3A3A3A;">
                                Nom de l'entreprise <span style="color: #D9542A;">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="nom" 
                                value="{{ old('nom', $entreprise->nom) }}"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                required
                            >
                            @error('nom')
                                <span style="color: #C62828; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Adresse -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #3A3A3A;">
                                Adresse complète <span style="color: #D9542A;">*</span>
                            </label>
                            <textarea 
                                name="adresse" 
                                rows="3"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem; font-family: 'Inter', sans-serif;"
                                required
                            >{{ old('adresse', $entreprise->adresse) }}</textarea>
                            @error('adresse')
                                <span style="color: #C62828; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Ville et Pays -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div>
                                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #3A3A3A;">
                                    Ville <span style="color: #D9542A;">*</span>
                                </label>
                                <select 
                                    name="ville" 
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                    required
                                >
                                    <option value="Abidjan" {{ old('ville', $entreprise->ville) == 'Abidjan' ? 'selected' : '' }}>Abidjan</option>
                                    <option value="Bouaké" {{ old('ville', $entreprise->ville) == 'Bouaké' ? 'selected' : '' }}>Bouaké</option>
                                    <option value="Yamoussoukro" {{ old('ville', $entreprise->ville) == 'Yamoussoukro' ? 'selected' : '' }}>Yamoussoukro</option>
                                    <option value="Daloa" {{ old('ville', $entreprise->ville) == 'Daloa' ? 'selected' : '' }}>Daloa</option>
                                    <option value="Korhogo" {{ old('ville', $entreprise->ville) == 'Korhogo' ? 'selected' : '' }}>Korhogo</option>
                                    <option value="San-Pédro" {{ old('ville', $entreprise->ville) == 'San-Pédro' ? 'selected' : '' }}>San-Pédro</option>
                                </select>
                                @error('ville')
                                    <span style="color: #C62828; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #3A3A3A;">
                                    Pays
                                </label>
                                <input 
                                    type="text" 
                                    name="pays" 
                                    value="{{ old('pays', $entreprise->pays) }}"
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                >
                                @error('pays')
                                    <span style="color: #C62828; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Numéro -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #3A3A3A;">
                                Numéro de téléphone
                            </label>
                            <input 
                                type="tel" 
                                name="numero" 
                                value="{{ old('numero', $entreprise->numero) }}"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                            >
                            @error('numero')
                                <span style="color: #C62828; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Coordonnées GPS -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div>
                                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #3A3A3A;">
                                    Latitude
                                </label>
                                <input 
                                    type="number" 
                                    step="0.00000001"
                                    name="lat" 
                                    value="{{ old('lat', $entreprise->lat) }}"
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                >
                                @error('lat')
                                    <span style="color: #C62828; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #3A3A3A;">
                                    Longitude
                                </label>
                                <input 
                                    type="number" 
                                    step="0.00000001"
                                    name="long" 
                                    value="{{ old('long', $entreprise->long) }}"
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                >
                                @error('long')
                                    <span style="color: #C62828; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Logo -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #3A3A3A;">
                                Nouveau logo (optionnel)
                            </label>
                            <input 
                                type="file" 
                                name="logo" 
                                accept="image/*"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                            >
                            <small style="color: #666; font-size: 0.75rem;">Laisser vide pour conserver le logo actuel</small>
                            @error('logo')
                                <span style="color: #C62828; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Statut -->
                        <div>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="statut" value="1" {{ old('statut', $entreprise->statut) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                                <span style="font-weight: 600; color: #3A3A3A;">Entreprise active</span>
                            </label>
                            <small style="color: #666; font-size: 0.75rem;">Une entreprise active peut recevoir des commandes</small>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #E5E5E5;">
                        <button type="submit" class="btn btn-primary">
                            Enregistrer les modifications
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
