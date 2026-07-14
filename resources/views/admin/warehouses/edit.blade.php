@extends('admin.layout')

@section('title', 'Modifier Entrepôt')
@section('page-title', 'Modifier un Entrepôt')

@section('content')
    <div style="max-width: 800px; margin: 0 auto;">
        @include('admin.partials.warehouse-tabs', ['warehouse' => $warehouse])

        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: #000000; margin-bottom: 1.5rem;">Informations de l'entrepôt</h2>

        <form action="{{ route('admin.warehouses.update', $warehouse->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 1.5rem;">
                <label for="name" style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">Nom de l'entrepôt</label>
                <input type="text" name="name" id="name" required 
                    style="width: 100%; padding: 0.75rem; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 1rem; transition: border-color 0.2s;"
                    value="{{ old('name', $warehouse->name) }}">
                @error('name') <span style="color: #EF4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</span> @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label for="address" style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">Adresse</label>
                    <input type="text" name="address" id="address" required 
                        style="width: 100%; padding: 0.75rem; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 1rem;"
                        value="{{ old('address', $warehouse->address) }}">
                    @error('address') <span style="color: #EF4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="city" style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">Ville</label>
                    <input type="text" name="city" id="city" required 
                        style="width: 100%; padding: 0.75rem; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 1rem;"
                        value="{{ old('city', $warehouse->city) }}">
                    @error('city') <span style="color: #EF4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label for="country" style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">Pays</label>
                    <input type="text" name="country" id="country" required 
                        style="width: 100%; padding: 0.75rem; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 1rem;"
                        value="{{ old('country', $warehouse->country) }}">
                </div>
                <div>
                    <label for="phone" style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">Téléphone (Optionnel)</label>
                    <input type="text" name="phone" id="phone" 
                        style="width: 100%; padding: 0.75rem; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 1rem;"
                        value="{{ old('phone', $warehouse->phone) }}">
                </div>
            </div>

            <div style="margin-bottom: 1.5rem; padding: 1rem; background: #F3F4F6; border-radius: 8px;">
                <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem; color: #374151;">Coordonnées GPS</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label for="latitude" style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">Latitude</label>
                        <input type="number" step="any" name="latitude" id="latitude" required 
                            style="width: 100%; padding: 0.75rem; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 1rem;"
                            value="{{ old('latitude', $warehouse->latitude) }}">
                        @error('latitude') <span style="color: #EF4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="longitude" style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">Longitude</label>
                        <input type="number" step="any" name="longitude" id="longitude" required 
                            style="width: 100%; padding: 0.75rem; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 1rem;"
                            value="{{ old('longitude', $warehouse->longitude) }}">
                        @error('longitude') <span style="color: #EF4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #374151; cursor: pointer;">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ $warehouse->is_active ? 'checked' : '' }} style="width: 18px; height: 18px;">
                    Entrepôt Actif
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                <a href="{{ route('admin.warehouses.index') }}" style="padding: 0.75rem 1.5rem; border: 1px solid #D1D5DB; border-radius: 6px; text-decoration: none; color: #374151; font-weight: 600;">Annuler</a>
                <button type="submit" style="background: #FF0000; color: white; padding: 0.75rem 2rem; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">Enregistrer</button>
            </div>
        </form>
        </div>
    </div>
@endsection
