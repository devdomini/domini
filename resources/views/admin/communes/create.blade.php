@extends('admin.layout')

@section('title', 'Nouvelle commune')
@section('page-title', 'Ajouter une commune')

@section('content')
    <div style="max-width: 800px; margin: 0 auto; background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: #000000; margin-bottom: 1.5rem;">Commune</h2>

        <form action="{{ route('admin.communes.store') }}" method="POST">
            @csrf

            <div style="margin-bottom: 1.5rem;">
                <label for="warehouse_id" style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Entrepôt <span style="color:#FF0000">*</span></label>
                <select name="warehouse_id" id="warehouse_id" required style="width: 100%; padding: 0.75rem; border: 1px solid #D1D5DB; border-radius: 6px;">
                    <option value="">— Choisir —</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }} ({{ $w->city }})</option>
                    @endforeach
                </select>
                @error('warehouse_id') <span style="color: #EF4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="nom" style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Nom de la commune <span style="color:#FF0000">*</span></label>
                <input type="text" name="nom" id="nom" required value="{{ old('nom') }}" style="width: 100%; padding: 0.75rem; border: 1px solid #D1D5DB; border-radius: 6px;">
                @error('nom') <span style="color: #EF4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label for="latitude" style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Latitude <span style="color:#6B7280; font-weight:400;">(optionnel)</span></label>
                    <input type="number" step="any" name="latitude" id="latitude" value="{{ old('latitude') }}" placeholder="ex. 5.36" style="width: 100%; padding: 0.75rem; border: 1px solid #D1D5DB; border-radius: 6px;">
                    @error('latitude') <span style="color: #EF4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="longitude" style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Longitude <span style="color:#6B7280; font-weight:400;">(optionnel)</span></label>
                    <input type="number" step="any" name="longitude" id="longitude" value="{{ old('longitude') }}" placeholder="ex. -3.99" style="width: 100%; padding: 0.75rem; border: 1px solid #D1D5DB; border-radius: 6px;">
                    @error('longitude') <span style="color: #EF4444; font-size: 0.875rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                <a href="{{ route('admin.communes.index') }}" style="padding: 0.75rem 1.5rem; border: 1px solid #D1D5DB; border-radius: 6px; text-decoration: none; color: #374151;">Annuler</a>
                <button type="submit" style="background: #FF0000; color: white; padding: 0.75rem 2rem; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">Enregistrer</button>
            </div>
        </form>
    </div>
@endsection
