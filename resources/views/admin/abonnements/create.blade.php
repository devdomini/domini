@extends('admin.layout')

@section('title', 'Nouvel Abonnement')
@section('page-title', 'Créer un Abonnement')

@section('content')
    <div style="max-width: 800px; margin: 0 auto;">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Nouvel Abonnement</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.abonnements.store') }}" method="POST">
                    @csrf

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

                    <!-- Représentant -->
                    <div class="form-group">
                        <label for="representant">Nom du Représentant *</label>
                        <input type="text" name="representant" id="representant" class="form-control" value="{{ old('representant') }}" required>
                        @error('representant')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Fonction -->
                    <div class="form-group">
                        <label for="fonction">Fonction *</label>
                        <input type="text" name="fonction" id="fonction" class="form-control" value="{{ old('fonction') }}" placeholder="Ex: DRH, Directeur Général..." required>
                        @error('fonction')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Numéro de téléphone -->
                    <div class="form-group">
                        <label for="numero">Numéro de Téléphone *</label>
                        <input type="tel" name="numero" id="numero" class="form-control" value="{{ old('numero') }}" placeholder="Ex: 0707123456" required>
                        @error('numero')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Type de subvention -->
                    <div class="form-group">
                        <label for="statut_subvention_commande">Type de Subvention *</label>
                        <select name="statut_subvention_commande" id="statut_subvention_commande" class="form-control" onchange="togglePourcentage()" required>
                            <option value="totale" {{ old('statut_subvention_commande') == 'totale' ? 'selected' : '' }}>Totale (100%)</option>
                            <option value="partielle" {{ old('statut_subvention_commande') == 'partielle' ? 'selected' : '' }}>Partielle</option>
                            <option value="aucune" {{ old('statut_subvention_commande') == 'aucune' ? 'selected' : '' }}>Aucune (0%)</option>
                        </select>
                        @error('statut_subvention_commande')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Pourcentage de subvention -->
                    <div class="form-group" id="pourcentage-group" style="display: none;">
                        <label for="pourcentage">Pourcentage de Subvention *</label>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <input type="number" name="pourcentage" id="pourcentage" class="form-control" value="{{ old('pourcentage', 100) }}" min="0" max="100" step="0.01" style="flex: 1;">
                            <span style="font-weight: 600; color: #3A3A3A;">%</span>
                        </div>
                        @error('pourcentage')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Nombre d'employés -->
                    <div class="form-group">
                        <label for="nbre_employe">Nombre d'Employés Couverts *</label>
                        <input type="number" name="nbre_employe" id="nbre_employe" class="form-control" value="{{ old('nbre_employe') }}" min="1" required>
                        @error('nbre_employe')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Durée -->
                    <div class="form-group">
                        <label for="duree_mois">Durée de l'Abonnement *</label>
                        <select name="duree_mois" id="duree_mois" class="form-control" required>
                            <option value="3" {{ old('duree_mois') == 3 ? 'selected' : '' }}>3 mois</option>
                            <option value="6" {{ old('duree_mois') == 6 ? 'selected' : '' }}>6 mois</option>
                            <option value="12" {{ old('duree_mois', 12) == 12 ? 'selected' : '' }}>12 mois (1 an) - Recommandé</option>
                            <option value="24" {{ old('duree_mois') == 24 ? 'selected' : '' }}>24 mois (2 ans)</option>
                            <option value="36" {{ old('duree_mois') == 36 ? 'selected' : '' }}>36 mois (3 ans)</option>
                        </select>
                        <small style="color: #666;">La date de début sera automatiquement aujourd'hui</small>
                        @error('duree_mois')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Boutons -->
                    <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #E5E5E5;">
                        <button type="submit" class="btn btn-primary" style="flex: 1;">
                            Créer l'Abonnement
                        </button>
                        <a href="{{ route('admin.abonnements.index') }}" class="btn btn-secondary">
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
function togglePourcentage() {
    const typeSubvention = document.getElementById('statut_subvention_commande').value;
    const pourcentageGroup = document.getElementById('pourcentage-group');
    const pourcentageInput = document.getElementById('pourcentage');
    
    if (typeSubvention === 'partielle') {
        pourcentageGroup.style.display = 'block';
        pourcentageInput.required = true;
    } else {
        pourcentageGroup.style.display = 'none';
        pourcentageInput.required = false;
        
        // Définir automatiquement le pourcentage
        if (typeSubvention === 'totale') {
            pourcentageInput.value = 100;
        } else if (typeSubvention === 'aucune') {
            pourcentageInput.value = 0;
        }
    }
}

// Appeler au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    togglePourcentage();
});
</script>
@endsection
