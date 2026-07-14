@extends('commercial.layout')

@section('title', 'Nouvelle entreprise')
@section('page-title', 'Ajouter une entreprise')

@section('content')
<div style="max-width: 900px;">
    <a href="{{ route('commercial.entreprises.index') }}" class="commercial-back-link">← Retour à la liste</a>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Informations de l'entreprise</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('commercial.entreprises.store') }}" enctype="multipart/form-data">
                @csrf
                <div style="display: grid; gap: 1.5rem;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Nom de l'entreprise <span style="color: #FF0000;">*</span></label>
                        <input type="text" name="nom" value="{{ old('nom') }}" required style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Adresse complète <span style="color: #FF0000;">*</span></label>
                        <textarea name="adresse" rows="3" required style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-family: inherit;">{{ old('adresse') }}</textarea>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Ville <span style="color: #FF0000;">*</span></label>
                            <select name="ville" required style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                                @foreach(['Abidjan','Bouaké','Yamoussoukro','Daloa','Korhogo','San-Pédro'] as $ville)
                                    <option value="{{ $ville }}" @selected(old('ville', 'Abidjan') === $ville)>{{ $ville }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Pays</label>
                            <input type="text" name="pays" value="{{ old('pays', "Côte d'Ivoire") }}" style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                        </div>
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Numéro de téléphone</label>
                        <input type="tel" name="numero" value="{{ old('numero') }}" placeholder="Ex: +225 27 20 00 00 00" style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                    </div>
                    <div class="commercial-casier-row">
                        <button type="button" id="btn-geoloc" class="btn btn-secondary">J'y suis (GPS)</button>
                        <span id="geoloc-status" class="commercial-muted"></span>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Latitude</label>
                            <input id="entreprise-lat" name="lat" value="{{ old('lat') }}" style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Longitude</label>
                            <input id="entreprise-long" name="long" value="{{ old('long') }}" style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                        </div>
                    </div>
                    @include('admin.entreprises._location_picker')
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Commune</label>
                        <select name="commune_id" style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                            <option value="">— Aucune —</option>
                            @foreach($communes as $c)
                                <option value="{{ $c->id }}" @selected(old('commune_id') == $c->id)>{{ $c->nom }} @if($c->warehouse) ({{ $c->warehouse->name }}) @endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Logo de l'entreprise</label>
                        <input type="file" name="logo" accept="image/*" style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                    </div>
                    <label style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="checkbox" name="statut" value="1" checked style="width: 18px; height: 18px;">
                        <span style="font-weight: 600;">Entreprise active</span>
                    </label>
                </div>
                <div class="commercial-actions" style="margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('commercial.entreprises.index') }}" class="btn btn-secondary">Annuler</a>
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
    document.getElementById('btn-geoloc')?.addEventListener('click', function() {
        var st = document.getElementById('geoloc-status');
        if (!navigator.geolocation) { st.textContent = 'GPS non disponible'; return; }
        st.textContent = 'Localisation…';
        navigator.geolocation.getCurrentPosition(function(pos) {
            document.getElementById('entreprise-lat').value = formatCoordInput(pos.coords.latitude);
            document.getElementById('entreprise-long').value = formatCoordInput(pos.coords.longitude);
            st.textContent = 'Position récupérée';
            if (typeof initEntrepriseLocationPicker === 'function') initEntrepriseLocationPicker();
        }, function() { st.textContent = 'Échec GPS'; }, { enableHighAccuracy: true, timeout: 15000 });
    });
    function initEntrepriseLocationPicker() {
        var mapEl = document.getElementById('entreprise-location-map');
        var latInput = document.getElementById('entreprise-lat');
        var lngInput = document.getElementById('entreprise-long');
        if (!mapEl || !latInput || !lngInput || typeof google === 'undefined') return;
        var lat = parseFloat(String(latInput.value).replace(',', '.')) || 5.354;
        var lng = parseFloat(String(lngInput.value).replace(',', '.')) || -3.981;
        var map = new google.maps.Map(mapEl, { center: { lat: lat, lng: lng }, zoom: 16 });
        var marker = new google.maps.Marker({ position: { lat: lat, lng: lng }, map: map, draggable: true });
        function sync(latLng) {
            latInput.value = formatCoordInput(latLng.lat());
            lngInput.value = formatCoordInput(latLng.lng());
        }
        marker.addListener('dragend', function() { sync(marker.getPosition()); });
        map.addListener('click', function(e) { marker.setPosition(e.latLng); sync(e.latLng); });
    }
    window.initEntrepriseLocationPickerCallback = function() { initEntrepriseLocationPicker(); };
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ urlencode($googleMapsApiKey) }}&callback=initEntrepriseLocationPickerCallback"></script>
@endsection
@endif
