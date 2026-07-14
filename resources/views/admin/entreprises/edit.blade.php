@extends('admin.layout')

@section('title', 'Modifier une entreprise')
@section('page-title', 'Modifier l\'entreprise')

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
                            <p style="font-weight: 600; color: #000000; margin-bottom: 0.5rem;">Logo actuel</p>
                            <img src="{{ asset('storage/' . $entreprise->logo) }}" alt="{{ $entreprise->nom }}" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #FF0000;">
                        </div>
                        @endif

                        <!-- Nom -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                                Nom de l'entreprise <span style="color: #FF0000;">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="nom" 
                                value="{{ old('nom', $entreprise->nom) }}"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
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
                                required
                            >{{ old('adresse', $entreprise->adresse) }}</textarea>
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
                                    <option value="Abidjan" {{ old('ville', $entreprise->ville) == 'Abidjan' ? 'selected' : '' }}>Abidjan</option>
                                    <option value="Bouaké" {{ old('ville', $entreprise->ville) == 'Bouaké' ? 'selected' : '' }}>Bouaké</option>
                                    <option value="Yamoussoukro" {{ old('ville', $entreprise->ville) == 'Yamoussoukro' ? 'selected' : '' }}>Yamoussoukro</option>
                                    <option value="Daloa" {{ old('ville', $entreprise->ville) == 'Daloa' ? 'selected' : '' }}>Daloa</option>
                                    <option value="Korhogo" {{ old('ville', $entreprise->ville) == 'Korhogo' ? 'selected' : '' }}>Korhogo</option>
                                    <option value="San-Pédro" {{ old('ville', $entreprise->ville) == 'San-Pédro' ? 'selected' : '' }}>San-Pédro</option>
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
                                    value="{{ old('pays', $entreprise->pays) }}"
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
                                value="{{ old('numero', $entreprise->numero) }}"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
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
                                    id="entreprise-lat"
                                    name="lat" 
                                    value="{{ old('lat', $entreprise->lat) }}"
                                    placeholder="ex. 5.3546081"
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                >
                                @error('lat')
                                    <span style="color: #CC0000; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                                @enderror
                                <span style="color: #666; font-size: 0.75rem;">≈ 5.35 pour Abidjan (ne pas inverser avec la longitude)</span>
                            </div>

                            <div>
                                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                                    Longitude
                                </label>
                                <input 
                                    type="text" 
                                    inputmode="decimal"
                                    id="entreprise-long"
                                    name="long" 
                                    value="{{ old('long', $entreprise->long) }}"
                                    placeholder="ex. -3.9814236"
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                >
                                @error('long')
                                    <span style="color: #CC0000; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                                @enderror
                                <span style="color: #666; font-size: 0.75rem;">≈ -3.98 ou -4.02 (valeur négative)</span>
                            </div>
                        </div>

                        @include('admin.entreprises._location_picker', ['googleMapsApiKey' => $googleMapsApiKey ?? null])

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
                                    <option value="{{ $commercial->id }}" {{ old('commercial_id', $entreprise->commercial_id) == $commercial->id ? 'selected' : '' }}>
                                        {{ $commercial->name }} ({{ $commercial->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('commercial_id')
                                <span style="color: #CC0000; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Commune -->
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
                                    <option value="{{ $c->id }}" {{ old('commune_id', $entreprise->commune_id) == $c->id ? 'selected' : '' }}>
                                        {{ $c->nom }} @if($c->warehouse) ({{ $c->warehouse->name }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('commune_id')
                                <span style="color: #CC0000; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Logo -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
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
                                <span style="color: #CC0000; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Statut -->
                        <div>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="statut" value="1" {{ old('statut', $entreprise->statut) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                                <span style="font-weight: 600; color: #000000;">Entreprise active</span>
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

@if(!empty($googleMapsApiKey))
@section('scripts')
<script>
    function formatCoordInput(n) {
        var s = Number(n).toFixed(7);
        return s.replace(/\.?0+$/, '');
    }

    function initEntrepriseLocationPicker() {
        var mapEl = document.getElementById('entreprise-location-map');
        var latInput = document.getElementById('entreprise-lat');
        var lngInput = document.getElementById('entreprise-long');
        if (!mapEl || !latInput || !lngInput) return;

        var lat = parseFloat(String(latInput.value).replace(',', '.')) || 5.354;
        var lng = parseFloat(String(lngInput.value).replace(',', '.')) || -3.981;

        var map = new google.maps.Map(mapEl, {
            center: { lat: lat, lng: lng },
            zoom: 16,
            mapTypeControl: false,
            streetViewControl: false,
        });

        var marker = new google.maps.Marker({
            position: { lat: lat, lng: lng },
            map: map,
            draggable: true,
        });

        function syncInputs(latLng) {
            latInput.value = formatCoordInput(latLng.lat());
            lngInput.value = formatCoordInput(latLng.lng());
        }

        marker.addListener('dragend', function() {
            syncInputs(marker.getPosition());
        });

        map.addListener('click', function(e) {
            marker.setPosition(e.latLng);
            syncInputs(e.latLng);
        });
    }

    window.initEntrepriseLocationPickerCallback = function() {
        initEntrepriseLocationPicker();
    };
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ urlencode($googleMapsApiKey) }}&callback=initEntrepriseLocationPickerCallback"></script>
@endsection
@endif
