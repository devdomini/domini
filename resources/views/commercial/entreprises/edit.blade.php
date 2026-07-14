@extends('commercial.layout')

@section('title', 'Modifier entreprise')
@section('page-title', 'Modifier — '.$entreprise->nom)

@section('content')
<div style="max-width: 900px;">
    <a href="{{ route('commercial.entreprises.show', $entreprise->id) }}" class="commercial-back-link">← Retour</a>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Modifier les informations</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('commercial.entreprises.update', $entreprise->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div style="display: grid; gap: 1.5rem;">
                    @if($entreprise->logo)
                    <div style="text-align: center; padding: 1rem; background-color: #FDFBF8; border-radius: 8px;">
                        <p style="font-weight: 600; margin-bottom: 0.5rem;">Logo actuel</p>
                        <img src="{{ asset('storage/'.$entreprise->logo) }}" alt="{{ $entreprise->nom }}" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #FF0000;">
                    </div>
                    @endif
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Nom <span style="color: #FF0000;">*</span></label>
                        <input type="text" name="nom" value="{{ old('nom', $entreprise->nom) }}" required style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Adresse <span style="color: #FF0000;">*</span></label>
                        <textarea name="adresse" rows="3" required style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-family: inherit;">{{ old('adresse', $entreprise->adresse) }}</textarea>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Ville <span style="color: #FF0000;">*</span></label>
                            <select name="ville" required style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                                @foreach(['Abidjan','Bouaké','Yamoussoukro','Daloa','Korhogo','San-Pédro'] as $ville)
                                    <option value="{{ $ville }}" @selected(old('ville', $entreprise->ville) === $ville)>{{ $ville }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Pays</label>
                            <input type="text" name="pays" value="{{ old('pays', $entreprise->pays ?? "Côte d'Ivoire") }}" style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                        </div>
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Numéro de téléphone</label>
                        <input type="tel" name="numero" value="{{ old('numero', $entreprise->numero) }}" style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Latitude</label>
                            <input id="entreprise-lat" name="lat" value="{{ old('lat', $entreprise->lat) }}" style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Longitude</label>
                            <input id="entreprise-long" name="long" value="{{ old('long', $entreprise->long) }}" style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                        </div>
                    </div>
                    @include('admin.entreprises._location_picker')
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Commune</label>
                        <select name="commune_id" style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                            <option value="">— Aucune —</option>
                            @foreach($communes as $c)
                                <option value="{{ $c->id }}" @selected(old('commune_id', $entreprise->commune_id) == $c->id)>{{ $c->nom }} @if($c->warehouse) ({{ $c->warehouse->name }}) @endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Nouveau logo</label>
                        <input type="file" name="logo" accept="image/*" style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                    </div>
                    <label style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="checkbox" name="statut" value="1" @checked(old('statut', $entreprise->statut)) style="width: 18px; height: 18px;">
                        <span style="font-weight: 600;">Entreprise active</span>
                    </label>
                </div>
                <div class="commercial-actions" style="margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@if(!empty($googleMapsApiKey))
@section('scripts')
@include('commercial.partials.map_scripts')
@endsection
@endif
