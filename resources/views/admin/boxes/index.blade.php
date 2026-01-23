@extends('admin.layout')

@section('title', 'Boxes & Casiers')
@section('page-title', 'Gestion des Boxes & Casiers')

@section('content')
    <!-- Actions Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #3A3A3A;">Toutes les boxes</h2>
            <p style="color: #666; margin-top: 0.25rem;">Gérez les points de livraison et leurs casiers intelligents</p>
        </div>
        <a href="{{ route('admin.boxes.create') }}" class="btn btn-primary">
            + Nouvelle Box
        </a>
    </div>

    <!-- Liste des boxes -->
    <div style="display: grid; gap: 1.5rem;">
        @forelse($boxes as $box)
        <div class="card">
            <div class="card-body">
                <div style="display: grid; grid-template-columns: 1fr auto; gap: 2rem;">
                    <!-- Info Box -->
                    <div>
                        <div style="display: flex; align-items: start; gap: 1rem; margin-bottom: 1rem;">
                            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #D9542A, #c13d18); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg style="width: 32px; height: 32px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                                    <h3 style="font-size: 1.25rem; font-weight: 700; margin: 0;">{{ $box->nom }}</h3>
                                    @if($box->est_actif)
                                        <span class="badge" style="background-color: #E8F5E9; color: #2d9248;">Actif</span>
                                    @else
                                        <span class="badge" style="background-color: #F5F5F5; color: #666;">Inactif</span>
                                    @endif
                                </div>
                                <div style="font-size: 0.875rem; color: #666; margin-bottom: 0.25rem;">{{ $box->ref }}</div>
                                <div style="font-size: 0.875rem; color: #666;">
                                    <strong>Entreprise:</strong> {{ $box->entreprise->nom }}
                                </div>
                                @if($box->adresse)
                                <div style="font-size: 0.875rem; color: #666; margin-top: 0.25rem; display: flex; align-items: center; gap: 0.5rem;">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $box->adresse }}
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Stats Casiers -->
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #E5E5E5;">
                            <div>
                                <div style="font-size: 0.75rem; color: #666; text-transform: uppercase; margin-bottom: 0.25rem;">Total Casiers</div>
                                <div style="font-size: 1.5rem; font-weight: 900; color: #3A3A3A;">{{ $box->capacite }}</div>
                            </div>
                            <div>
                                <div style="font-size: 0.75rem; color: #666; text-transform: uppercase; margin-bottom: 0.25rem;">Libres</div>
                                <div style="font-size: 1.5rem; font-weight: 900; color: #10B981;">{{ $box->casiersLibres() }}</div>
                            </div>
                            <div>
                                <div style="font-size: 0.75rem; color: #666; text-transform: uppercase; margin-bottom: 0.25rem;">Occupés</div>
                                <div style="font-size: 1.5rem; font-weight: 900; color: #EF4444;">{{ $box->casiersOccupes() }}</div>
                            </div>
                            <div>
                                <div style="font-size: 0.75rem; color: #666; text-transform: uppercase; margin-bottom: 0.25rem;">Taux d'occupation</div>
                                <div style="font-size: 1.5rem; font-weight: 900; color: #F7B801;">
                                    {{ $box->capacite > 0 ? round(($box->casiersOccupes() / $box->capacite) * 100) : 0 }}%
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <a href="{{ route('admin.boxes.show', $box->id) }}" class="btn btn-primary" style="text-align: center;">
                            Voir Détails
                        </a>
                        <a href="{{ route('admin.boxes.edit', $box->id) }}" class="btn btn-secondary" style="text-align: center;">
                            Modifier
                        </a>
                        <button onclick="showAjouterCasiersModal({{ $box->id }})" class="btn" style="background-color: #10B981; color: white; text-align: center;">
                            + Ajouter Casiers
                        </button>
                        <form action="{{ route('admin.boxes.toggle', $box->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn" style="width: 100%; background-color: {{ $box->est_actif ? '#F7B801' : '#10B981' }}; color: white;">
                                {{ $box->est_actif ? 'Désactiver' : 'Activer' }}
                            </button>
                        </form>
                        <form action="{{ route('admin.boxes.destroy', $box->id) }}" method="POST" onsubmit="return confirm('Supprimer cette box et tous ses casiers ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="width: 100%;">
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Ajouter Casiers -->
        <div id="ajouterCasiersModal{{ $box->id }}" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
            <div style="background: white; border-radius: 12px; padding: 2rem; max-width: 400px; width: 90%;">
                <h3 style="margin-top: 0;">Ajouter des casiers</h3>
                <form action="{{ route('admin.boxes.casiers.ajouter', $box->id) }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem;">Nombre de casiers à ajouter *</label>
                        <input type="number" name="nombre" min="1" max="50" required style="width: 100%; padding: 0.75rem; border: 1px solid #E5E5E5; border-radius: 8px;">
                        <small style="color: #666; font-size: 0.75rem;">Maximum: 50 casiers à la fois</small>
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" class="btn btn-primary" style="flex: 1;">Ajouter</button>
                        <button type="button" onclick="hideAjouterCasiersModal({{ $box->id }})" class="btn btn-secondary">Annuler</button>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 4rem;">
                <svg style="width: 80px; height: 80px; margin: 0 auto 1.5rem; opacity: 0.3;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">Aucune box</h3>
                <p style="color: #666; margin-bottom: 1.5rem;">Commencez par créer votre première box</p>
                <a href="{{ route('admin.boxes.create') }}" class="btn btn-primary">
                    + Créer une Box
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    {{ $boxes->links('vendor.pagination.domini') }}
@endsection

@section('scripts')
<script>
function showAjouterCasiersModal(id) {
    document.getElementById('ajouterCasiersModal' + id).style.display = 'flex';
}

function hideAjouterCasiersModal(id) {
    document.getElementById('ajouterCasiersModal' + id).style.display = 'none';
}

// Close modal on outside click
document.querySelectorAll('[id^="ajouterCasiersModal"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });
});
</script>
@endsection
