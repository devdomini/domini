@extends('admin.layout')

@section('title', 'Abonnements')
@section('page-title', 'Gestion des Abonnements')

@section('content')
    <!-- Actions Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #000000;">Tous les abonnements</h2>
            <p style="color: #666; margin-top: 0.25rem;">Gérez les souscriptions des entreprises partenaires</p>
        </div>
        <a href="{{ route('admin.abonnements.create') }}" class="btn btn-primary">
            + Nouvel Abonnement
        </a>
    </div>

    <!-- Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div style="background: linear-gradient(135deg, #1A1A1A, #000000); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Total Abonnements</div>
            <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ $stats['total'] }}</div>
        </div>

        <div style="background: linear-gradient(135deg, #10B981, #059669); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Actifs</div>
            <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ $stats['actifs'] }}</div>
        </div>

        <div style="background: linear-gradient(135deg, #CC0000, #990000); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Suspendus</div>
            <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ $stats['suspendus'] }}</div>
        </div>

        <div style="background: linear-gradient(135deg, #EF4444, #DC2626); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Résiliés</div>
            <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ $stats['resilies'] }}</div>
        </div>
    </div>

    <!-- Filtres et Actions -->
    <div class="card" style="margin-bottom: 2rem;">
        <div class="card-body">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <form method="GET" style="display: flex; gap: 1rem; flex: 1;">
                    <select name="status" class="form-control" style="max-width: 200px;" onchange="this.form.submit()">
                        <option value="">Tous les statuts</option>
                        <option value="actif" {{ request('status') == 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="suspendu" {{ request('status') == 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                        <option value="resilie" {{ request('status') == 'resilie' ? 'selected' : '' }}>Résilié</option>
                        <option value="expire" {{ request('status') == 'expire' ? 'selected' : '' }}>Expiré</option>
                    </select>

                    <select name="entreprise" class="form-control" style="max-width: 300px;" onchange="this.form.submit()">
                        <option value="">Toutes les entreprises</option>
                        @foreach($entreprises as $entreprise)
                            <option value="{{ $entreprise->id }}" {{ request('entreprise') == $entreprise->id ? 'selected' : '' }}>
                                {{ $entreprise->nom }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>

    <!-- Tableau des abonnements -->
    <div class="card">
        <div class="card-body">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Entreprise</th>
                            <th>Représentant</th>
                            <th>Contact</th>
                            <th>Employés</th>
                            <th>Subvention</th>
                            <th>Période</th>
                            <th>Statut</th>
                            <th style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($abonnements as $abonnement)
                        <tr>
                            <td>
                                <div style="font-weight: 600;">{{ $abonnement->entreprise->nom }}</div>
                                <div style="font-size: 0.75rem; color: #999;">{{ $abonnement->entreprise->ville }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ $abonnement->representant }}</div>
                                <div style="font-size: 0.75rem; color: #999;">{{ $abonnement->fonction }}</div>
                            </td>
                            <td>{{ $abonnement->numero }}</td>
                            <td style="text-align: center; font-weight: 700; color: #FF0000;">{{ $abonnement->nbre_employe }}</td>
                            <td>
                                @if($abonnement->statut_subvention_commande === 'totale')
                                    <span style="color: #10B981; font-weight: 600;">100%</span>
                                @elseif($abonnement->statut_subvention_commande === 'partielle')
                                    <span style="color: #CC0000; font-weight: 600;">{{ $abonnement->pourcentage }}%</span>
                                @else
                                    <span style="color: #999;">Aucune</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-size: 0.875rem;">{{ $abonnement->date_debut->format('d/m/Y') }}</div>
                                <div style="font-size: 0.875rem;">{{ $abonnement->date_fin->format('d/m/Y') }}</div>
                                @if(!$abonnement->estExpire())
                                    <div style="font-size: 0.75rem; color: #10B981; font-weight: 600; margin-top: 0.25rem;">
                                        {{ $abonnement->joursRestants() }} jours restants
                                    </div>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'actif' => 'background-color: #E8F5E9; color: #2d9248;',
                                        'suspendu' => 'background-color: #FFF3E0; color: #E65100;',
                                        'resilie' => 'background-color: #FFEBEE; color: #CC0000;',
                                        'expire' => 'background-color: #F5F5F5; color: #666;'
                                    ];
                                    $statusLabels = [
                                        'actif' => 'Actif',
                                        'suspendu' => 'Suspendu',
                                        'resilie' => 'Résilié',
                                        'expire' => 'Expiré'
                                    ];
                                @endphp
                                <span class="badge" style="{{ $statusColors[$abonnement->status] ?? '' }}">
                                    {{ $statusLabels[$abonnement->status] ?? $abonnement->status }}
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <a href="{{ route('admin.abonnements.show', $abonnement->id) }}" class="btn-icon btn-icon-info" title="Voir">
                                        <svg style="width: 14px; height: 14px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    @if($abonnement->status === 'actif')
                                        <button onclick="showSuspendreModal({{ $abonnement->id }})" class="btn-icon" style="background-color: #CC0000;" title="Suspendre">
                                            <svg style="width: 14px; height: 14px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>

                                        <button onclick="showResilierModal({{ $abonnement->id }})" class="btn-icon" style="background-color: #EF4444;" title="Résilier">
                                            <svg style="width: 14px; height: 14px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    @endif

                                    @if($abonnement->status === 'suspendu')
                                        <form action="{{ route('admin.abonnements.reactiver', $abonnement->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn-icon" style="background-color: #10B981;" title="Réactiver" onclick="return confirm('Voulez-vous réactiver cet abonnement ?')">
                                                <svg style="width: 14px; height: 14px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    @if(in_array($abonnement->status, ['expire', 'resilie']) || $abonnement->joursRestants() < 30)
                                        <button onclick="showRenouvelerModal({{ $abonnement->id }})" class="btn-icon" style="background-color: #3B82F6;" title="Renouveler">
                                            <svg style="width: 14px; height: 14px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                        </button>
                                    @endif

                                    @if($abonnement->status === 'actif')
                                        <a href="{{ route('admin.abonnements.edit', $abonnement->id) }}" class="btn-icon" style="background-color: #CC0000;" title="Modifier">
                                            <svg style="width: 14px; height: 14px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Résilier -->
                        <div id="resilierModal{{ $abonnement->id }}" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
                            <div style="background: white; border-radius: 12px; padding: 2rem; max-width: 500px; width: 90%;">
                                <h3 style="margin-top: 0;">Résilier l'abonnement</h3>
                                <form action="{{ route('admin.abonnements.resilier', $abonnement->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div style="margin-bottom: 1rem;">
                                        <label style="display: block; margin-bottom: 0.5rem;">Raison de la résiliation *</label>
                                        <textarea name="raison" rows="4" required style="width: 100%; padding: 0.75rem; border: 1px solid #E5E5E5; border-radius: 8px;"></textarea>
                                    </div>
                                    <div style="display: flex; gap: 1rem;">
                                        <button type="submit" class="btn btn-danger" style="flex: 1;">Confirmer la résiliation</button>
                                        <button type="button" onclick="hideResilierModal({{ $abonnement->id }})" class="btn btn-secondary">Annuler</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Modal Suspendre -->
                        <div id="suspendreModal{{ $abonnement->id }}" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
                            <div style="background: white; border-radius: 12px; padding: 2rem; max-width: 400px; width: 90%;">
                                <h3 style="margin-top: 0;">Suspendre l'abonnement</h3>
                                <p>Voulez-vous vraiment suspendre cet abonnement ?</p>
                                <form action="{{ route('admin.abonnements.suspendre', $abonnement->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div style="display: flex; gap: 1rem;">
                                        <button type="submit" class="btn" style="flex: 1; background-color: #CC0000; color: white;">Confirmer</button>
                                        <button type="button" onclick="hideSuspendreModal({{ $abonnement->id }})" class="btn btn-secondary">Annuler</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Modal Renouveler -->
                        <div id="renouvelerModal{{ $abonnement->id }}" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
                            <div style="background: white; border-radius: 12px; padding: 2rem; max-width: 400px; width: 90%;">
                                <h3 style="margin-top: 0;">Renouveler l'abonnement</h3>
                                <form action="{{ route('admin.abonnements.renouveler', $abonnement->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div style="margin-bottom: 1rem;">
                                        <label style="display: block; margin-bottom: 0.5rem;">Durée (en mois) *</label>
                                        <select name="duree_mois" required style="width: 100%; padding: 0.75rem; border: 1px solid #E5E5E5; border-radius: 8px;">
                                            <option value="3">3 mois</option>
                                            <option value="6">6 mois</option>
                                            <option value="12" selected>12 mois (1 an)</option>
                                            <option value="24">24 mois (2 ans)</option>
                                            <option value="36">36 mois (3 ans)</option>
                                        </select>
                                    </div>
                                    <div style="display: flex; gap: 1rem;">
                                        <button type="submit" class="btn btn-primary" style="flex: 1;">Renouveler</button>
                                        <button type="button" onclick="hideRenouvelerModal({{ $abonnement->id }})" class="btn btn-secondary">Annuler</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3rem; color: #666;">
                                Aucun abonnement trouvé
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    {{ $abonnements->links('vendor.pagination.domini') }}
@endsection

@section('scripts')
<script>
function showResilierModal(id) {
    document.getElementById('resilierModal' + id).style.display = 'flex';
}

function hideResilierModal(id) {
    document.getElementById('resilierModal' + id).style.display = 'none';
}

function showSuspendreModal(id) {
    document.getElementById('suspendreModal' + id).style.display = 'flex';
}

function hideSuspendreModal(id) {
    document.getElementById('suspendreModal' + id).style.display = 'none';
}

function showRenouvelerModal(id) {
    document.getElementById('renouvelerModal' + id).style.display = 'flex';
}

function hideRenouvelerModal(id) {
    document.getElementById('renouvelerModal' + id).style.display = 'none';
}

// Close modal on outside click
document.querySelectorAll('[id^="resilierModal"], [id^="suspendreModal"], [id^="renouvelerModal"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });
});
</script>
@endsection
