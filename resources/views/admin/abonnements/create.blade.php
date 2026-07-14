@extends('admin.layout')

@section('title', 'Nouvel Abonnement')
@section('page-title', 'Créer un abonnement')

@section('styles')
<style>
    .abo-form-wrap { max-width: 880px; margin: 0 auto; }
    .abo-field-label { display: block; font-weight: 600; margin-bottom: 0.45rem; color: #000000; font-size: 0.9rem; }
    .abo-field-label .req { color: #FF0000; }
    .abo-input,
    .abo-select {
        width: 100%;
        padding: 0.75rem 0.9rem;
        border: 2px solid #E5E5E5;
        border-radius: 10px;
        font-size: 1rem;
        font-family: inherit;
        background: #fff;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }
    .abo-input:focus,
    .abo-select:focus {
        outline: none;
        border-color: #FF0000;
        box-shadow: 0 0 0 3px rgba(255, 0, 0, 0.15);
    }
    .abo-select { cursor: pointer; appearance: auto; }
    .abo-hint { display: block; margin-top: 0.4rem; font-size: 0.8rem; color: #666; line-height: 1.4; }
    .abo-error { color: #CC0000; font-size: 0.8rem; margin-top: 0.35rem; display: block; }
    .abo-section {
        background: linear-gradient(180deg, #fafafa 0%, #fff 100%);
        border: 1px solid #E8E8E8;
        border-radius: 14px;
        padding: 1.35rem 1.5rem;
        margin-bottom: 1.35rem;
    }
    .abo-section-title {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #888;
        margin: 0 0 1.1rem 0;
        padding-bottom: 0.65rem;
        border-bottom: 2px solid #f0f0f0;
    }
    .abo-grid-2 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.15rem;
    }
    .abo-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.85rem;
        margin-top: 1.75rem;
        padding-top: 1.5rem;
        border-top: 1px solid #E5E5E5;
    }
    .abo-actions .btn-primary { flex: 1; min-width: 200px; padding: 0.85rem 1.25rem; font-weight: 700; border-radius: 10px; }
    .abo-actions .btn-secondary { padding: 0.85rem 1.35rem; font-weight: 600; border-radius: 10px; }
    .abo-pct-wrap { display: flex; align-items: center; gap: 0.65rem; }
    .abo-pct-wrap .abo-input { flex: 1; max-width: 140px; }
    .abo-pct-suffix { font-weight: 700; color: #000000; font-size: 1.1rem; }
</style>
@endsection

@section('content')
    <div class="abo-form-wrap">
        <div style="margin-bottom: 1.5rem;">
            <a href="{{ route('admin.abonnements.index') }}" style="color: #FF0000; text-decoration: none; font-weight: 600; font-size: 0.95rem;">
                ← Retour à la liste des abonnements
            </a>
        </div>

        <div class="card" style="border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.06);">
            <div class="card-header" style="background: linear-gradient(135deg, #FF0000 0%, #CC0000 100%); padding: 1.25rem 1.5rem; border-bottom: none;">
                <h2 class="card-title" style="margin: 0; color: #fff; font-size: 1.35rem; font-weight: 800;">Nouvel abonnement</h2>
                <p style="margin: 0.4rem 0 0 0; color: rgba(255,255,255,0.9); font-size: 0.88rem;">Renseignez l’entreprise, le contact et les paramètres de subvention.</p>
            </div>
            <div class="card-body" style="padding: 1.75rem 1.5rem 2rem;">
                <form action="{{ route('admin.abonnements.store') }}" method="POST">
                    @csrf

                    <div class="abo-section">
                        <h3 class="abo-section-title">Entreprise</h3>
                        <div>
                            <label for="id_entreprise" class="abo-field-label">Entreprise <span class="req">*</span></label>
                            <select name="id_entreprise" id="id_entreprise" class="abo-select" required>
                                <option value="">Sélectionner une entreprise</option>
                                @foreach($entreprises as $entreprise)
                                    <option value="{{ $entreprise->id }}" {{ old('id_entreprise') == $entreprise->id ? 'selected' : '' }}>
                                        {{ $entreprise->nom }} — {{ $entreprise->ville }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_entreprise')
                                <span class="abo-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="abo-section">
                        <h3 class="abo-section-title">Contact & représentant</h3>
                        <div class="abo-grid-2">
                            <div>
                                <label for="representant" class="abo-field-label">Nom du représentant <span class="req">*</span></label>
                                <input type="text" name="representant" id="representant" class="abo-input" value="{{ old('representant') }}" placeholder="Nom et prénom" required autocomplete="name">
                                @error('representant')
                                    <span class="abo-error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="fonction" class="abo-field-label">Fonction <span class="req">*</span></label>
                                <input type="text" name="fonction" id="fonction" class="abo-input" value="{{ old('fonction') }}" placeholder="Ex. DRH, Directeur général…" required>
                                @error('fonction')
                                    <span class="abo-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div style="margin-top: 1.15rem;">
                            <label for="numero" class="abo-field-label">Numéro de téléphone <span class="req">*</span></label>
                            <input type="tel" name="numero" id="numero" class="abo-input" value="{{ old('numero') }}" placeholder="Ex. 07 01 23 45 67" required autocomplete="tel">
                            @error('numero')
                                <span class="abo-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="abo-section">
                        <h3 class="abo-section-title">Subvention</h3>
                        <div class="abo-grid-2">
                            <div>
                                <label for="statut_subvention_commande" class="abo-field-label">Type de subvention <span class="req">*</span></label>
                                <select name="statut_subvention_commande" id="statut_subvention_commande" class="abo-select" required onchange="togglePourcentage()">
                                    <option value="totale" {{ old('statut_subvention_commande') == 'totale' ? 'selected' : '' }}>Totale (100 %)</option>
                                    <option value="partielle" {{ old('statut_subvention_commande') == 'partielle' ? 'selected' : '' }}>Partielle</option>
                                    <option value="aucune" {{ old('statut_subvention_commande') == 'aucune' ? 'selected' : '' }}>Aucune (0 %)</option>
                                </select>
                                @error('statut_subvention_commande')
                                    <span class="abo-error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div id="pourcentage-group" style="display: none;">
                                <label for="pourcentage" class="abo-field-label">Pourcentage <span class="req">*</span></label>
                                <div class="abo-pct-wrap">
                                    <input type="number" name="pourcentage" id="pourcentage" class="abo-input" value="{{ old('pourcentage', 100) }}" min="0" max="100" step="0.01">
                                    <span class="abo-pct-suffix">%</span>
                                </div>
                                @error('pourcentage')
                                    <span class="abo-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="abo-section">
                        <h3 class="abo-section-title">Paramètres</h3>
                        <div class="abo-grid-2">
                            <div>
                                <label for="nbre_employe" class="abo-field-label">Nombre d’employés couverts <span class="req">*</span></label>
                                <input type="number" name="nbre_employe" id="nbre_employe" class="abo-input" value="{{ old('nbre_employe') }}" min="1" placeholder="Ex. 50" required>
                                @error('nbre_employe')
                                    <span class="abo-error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="duree_mois" class="abo-field-label">Durée de l’abonnement <span class="req">*</span></label>
                                <select name="duree_mois" id="duree_mois" class="abo-select" required>
                                    <option value="3" {{ old('duree_mois') == 3 ? 'selected' : '' }}>3 mois</option>
                                    <option value="6" {{ old('duree_mois') == 6 ? 'selected' : '' }}>6 mois</option>
                                    <option value="12" {{ old('duree_mois', 12) == 12 ? 'selected' : '' }}>12 mois (1 an) — recommandé</option>
                                    <option value="24" {{ old('duree_mois') == 24 ? 'selected' : '' }}>24 mois (2 ans)</option>
                                    <option value="36" {{ old('duree_mois') == 36 ? 'selected' : '' }}>36 mois (3 ans)</option>
                                </select>
                                <span class="abo-hint">La date de début est fixée à aujourd’hui ; la date de fin est calculée automatiquement.</span>
                                @error('duree_mois')
                                    <span class="abo-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="abo-actions">
                        <button type="submit" class="btn btn-primary">Créer l’abonnement</button>
                        <a href="{{ route('admin.abonnements.index') }}" class="btn btn-secondary">Annuler</a>
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
        if (typeSubvention === 'totale') pourcentageInput.value = 100;
        else if (typeSubvention === 'aucune') pourcentageInput.value = 0;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    togglePourcentage();
});
</script>
@endsection
