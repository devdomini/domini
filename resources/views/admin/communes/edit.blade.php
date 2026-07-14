@extends('admin.layout')

@section('title', 'Modifier commune')
@section('page-title', 'Modifier la commune')

@section('content')
    <div style="max-width: 800px; margin: 0 auto; background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <form action="{{ route('admin.communes.update', $commune) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 1.5rem;">
                <label for="warehouse_id" style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Entrepôt <span style="color:#E95322">*</span></label>
                <select name="warehouse_id" id="warehouse_id" required style="width: 100%; padding: 0.75rem; border: 1px solid #D1D5DB; border-radius: 6px;">
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" {{ old('warehouse_id', $commune->warehouse_id) == $w->id ? 'selected' : '' }}>{{ $w->name }} ({{ $w->city }})</option>
                    @endforeach
                </select>
                @error('warehouse_id') <span style="color: #EF4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="nom" style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Nom <span style="color:#E95322">*</span></label>
                <input type="text" name="nom" id="nom" required value="{{ old('nom', $commune->nom) }}" style="width: 100%; padding: 0.75rem; border: 1px solid #D1D5DB; border-radius: 6px;">
                @error('nom') <span style="color: #EF4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label for="latitude" style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Latitude <span style="color:#6B7280; font-weight:400;">(optionnel)</span></label>
                    <input type="number" step="any" name="latitude" id="latitude" value="{{ old('latitude', $commune->latitude) }}" placeholder="ex. 5.36" style="width: 100%; padding: 0.75rem; border: 1px solid #D1D5DB; border-radius: 6px;">
                    @error('latitude') <span style="color: #EF4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="longitude" style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Longitude <span style="color:#6B7280; font-weight:400;">(optionnel)</span></label>
                    <input type="number" step="any" name="longitude" id="longitude" value="{{ old('longitude', $commune->longitude) }}" placeholder="ex. -3.99" style="width: 100%; padding: 0.75rem; border: 1px solid #D1D5DB; border-radius: 6px;">
                    @error('longitude') <span style="color: #EF4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                <a href="{{ route('admin.communes.index') }}" style="padding: 0.75rem 1.5rem; border: 1px solid #D1D5DB; border-radius: 6px; text-decoration: none;">Annuler</a>
                <button type="submit" style="background: #E95322; color: white; padding: 0.75rem 2rem; border: none; border-radius: 6px; font-weight: 600;">Mettre à jour</button>
            </div>
        </form>
    </div>
@endsection
