@extends('admin.layout')

@section('title', 'Nouvelle Box')
@section('page-title', 'Créer une Box')

@section('content')
    <div style="max-width: 800px; margin: 0 auto;">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Nouvelle Box de Livraison</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.boxes.store') }}" method="POST">
                    @csrf

                    <!-- Nom de la box -->
                    <div class="form-group">
                        <label for="nom">Nom de la Box *</label>
                        <input type="text" name="nom" id="nom" class="form-control" value="{{ old('nom') }}" placeholder="Ex: Box Orange Plateau" required>
                        <small style="color: #666;">Nom descriptif pour identifier facilement la box</small>
                        @error('nom')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Entreprise -->
                    <div class="form-group">
                        <label for="id_entreprise">Entreprise *</label>
                        <select name="id_entreprise" id="id_entreprise" class="form-control" required>
                            <option value="">Sélectionner une entreprise</option>
                            @foreach($entreprises as $entreprise)
                                <option value="{{ $entreprise->id }}" {{ old('id_entreprise') == $entreprise->id ? 'selected' : '' }}>
                                    {{ $entreprise->nom }} - {{ $entreprise->ville }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_entreprise')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Adresse -->
                    <div class="form-group">
                        <label for="adresse">Adresse</label>
                        <textarea name="adresse" id="adresse" class="form-control" rows="2" placeholder="Ex: Boulevard Lagunaire, Zone 4C, Plateau">{{ old('adresse') }}</textarea>
                        @error('adresse')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Coordonnées GPS -->
                    <div style="background: #F9F9F9; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem; color: #3A3A3A; display: flex; align-items: center; gap: 0.5rem;">
                            <svg style="width: 20px; height: 20px; color: #D9542A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Coordonnées GPS (Optionnel)
                        </h4>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label for="lat">Latitude</label>
                                <input type="number" name="lat" id="lat" class="form-control" value="{{ old('lat') }}" step="0.0000001" placeholder="Ex: 5.3364">
                                @error('lat')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group" style="margin-bottom: 0;">
                                <label for="long">Longitude</label>
                                <input type="number" name="long" id="long" class="form-control" value="{{ old('long') }}" step="0.0000001" placeholder="Ex: -4.0267">
                                @error('long')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <small style="color: #666; display: block; margin-top: 0.5rem; display: flex; align-items: start; gap: 0.5rem;">
                            <svg style="width: 14px; height: 14px; color: #F7B801; flex-shrink: 0; margin-top: 2px;" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                            </svg>
                            <span>Les coordonnées GPS permettent de localiser précisément la box sur une carte</span>
                        </small>
                    </div>

                    <!-- Capacité (nombre de casiers) -->
                    <div class="form-group">
                        <label for="capacite">Nombre de Casiers *</label>
                        <input type="number" name="capacite" id="capacite" class="form-control" value="{{ old('capacite', 30) }}" min="1" max="200" required>
                        <small style="color: #666; display: flex; align-items: start; gap: 0.5rem;">
                            <svg style="width: 14px; height: 14px; color: #F7B801; flex-shrink: 0; margin-top: 2px;" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                            </svg>
                            <span>Les casiers seront générés automatiquement avec leurs QR codes uniques</span>
                        </small>
                        @error('capacite')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Info box -->
                    <div style="background: linear-gradient(135deg, #E8F5E9, #C8E6C9); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        <div style="display: flex; gap: 1rem; align-items: start;">
                            <svg style="width: 32px; height: 32px; color: #2d9248; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <div style="font-weight: 600; color: #2d9248; margin-bottom: 0.5rem;">Génération Automatique</div>
                                <div style="font-size: 0.875rem; color: #1b5e20; line-height: 1.6;">
                                    Lors de la création de cette box, <strong id="capacite-display">30</strong> casiers seront automatiquement générés avec :
                                </div>
                                <ul style="margin-top: 0.5rem; margin-bottom: 0; padding-left: 1.5rem; font-size: 0.875rem; color: #1b5e20;">
                                    <li>Références uniques (ex: BOX-XXX-C001, BOX-XXX-C002...)</li>
                                    <li>QR codes uniques pour chaque casier</li>
                                    <li>Numérotation automatique</li>
                                    <li>Statut "Libre" par défaut</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #E5E5E5;">
                        <button type="submit" class="btn btn-primary" style="flex: 1;">
                            Créer la Box et Générer les Casiers
                        </button>
                        <a href="{{ route('admin.boxes.index') }}" class="btn btn-secondary">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
// Mettre à jour le nombre de casiers dans l'info box
document.getElementById('capacite').addEventListener('input', function() {
    const capacite = this.value || 30;
    document.getElementById('capacite-display').textContent = capacite;
});
</script>
@endsection
