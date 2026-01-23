@extends('admin.layout')

@section('title', 'Commandes')
@section('page-title', 'Gestion des Commandes')

@section('content')
    <!-- Actions Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #3A3A3A;">Toutes les commandes</h2>
            <p style="color: #666; margin-top: 0.25rem;">Gérez les commandes des employés</p>
        </div>
    </div>

    <!-- Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div style="background: linear-gradient(135deg, #D9542A, #c13d18); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Total Commandes</div>
            <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ \App\Models\Commande::count() }}</div>
        </div>
        <div style="background: linear-gradient(135deg, #F7B801, #e5a900); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">En Attente</div>
            <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ \App\Models\Commande::where('statut_commande', 'en_attente')->count() }}</div>
        </div>
        <div style="background: linear-gradient(135deg, #10B981, #059669); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Confirmées</div>
            <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ \App\Models\Commande::where('statut_commande', 'confirmee')->count() }}</div>
        </div>
        <div style="background: linear-gradient(135deg, #3A3A3A, #2A2A2A); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Annulées</div>
            <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ \App\Models\Commande::where('statut_commande', 'annulee')->count() }}</div>
        </div>
    </div>

    @if(session('success'))
    <div style="background-color: #E8F5E9; color: #2d9248; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filters -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <form method="GET" style="padding: 1rem; display: flex; gap: 1rem; flex-wrap: wrap;">
            <select name="statut_commande" style="padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px; background: white;">
                <option value="">Statut commande</option>
                <option value="en_attente">En attente</option>
                <option value="confirmee">Confirmée</option>
                <option value="annulee">Annulée</option>
                <option value="terminee">Terminée</option>
            </select>
            
            <select name="statut_preparation" style="padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px; background: white;">
                <option value="">Statut préparation</option>
                <option value="en_attente">En attente</option>
                <option value="en_cours">En cours</option>
                <option value="prete">Prête</option>
            </select>

            <select name="statut_livraison" style="padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px; background: white;">
                <option value="">Statut livraison</option>
                <option value="en_attente">En attente</option>
                <option value="en_cours">En cours</option>
                <option value="livree">Livrée</option>
                <option value="echec">Échec</option>
            </select>
            
            <button type="submit" style="padding: 0.5rem 1rem; background: #D9542A; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                Filtrer
            </button>
        </form>
    </div>

    <!-- Commandes Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Employé</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Préparation</th>
                            <th>Livraison</th>
                            <th>Livreur</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commandes as $commande)
                        <tr>
                            <td>
                                <div style="font-weight: 600;">{{ $commande->ref }}</div>
                            </td>
                            <td>
                                <div>{{ $commande->employe->name ?? 'N/A' }}</div>
                                <div style="font-size: 0.75rem; color: #999;">{{ $commande->employe->email ?? '' }}</div>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #D9542A;">{{ number_format($commande->montant_total, 0, ',', ' ') }} FCFA</span>
                            </td>
                            <td>
                                @php
                                    $badges = [
                                        'en_attente' => 'background-color: #FFF3E0; color: #E65100;',
                                        'confirmee' => 'background-color: #E8F5E9; color: #2d9248;',
                                        'annulee' => 'background-color: #FFEBEE; color: #C62828;',
                                        'terminee' => 'background-color: #E3F2FD; color: #1976D2;'
                                    ];
                                    $labels = [
                                        'en_attente' => 'En attente',
                                        'confirmee' => 'Confirmée',
                                        'annulee' => 'Annulée',
                                        'terminee' => 'Terminée'
                                    ];
                                @endphp
                                <span class="badge" style="{{ $badges[$commande->statut_commande] ?? '' }}">
                                    {{ $labels[$commande->statut_commande] ?? $commande->statut_commande }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $prepaBadges = [
                                        'en_attente' => 'background-color: #F5F5F5; color: #666;',
                                        'en_cours' => 'background-color: #E3F2FD; color: #1976D2;',
                                        'prete' => 'background-color: #E8F5E9; color: #2d9248;'
                                    ];
                                    $prepaLabels = [
                                        'en_attente' => 'En attente',
                                        'en_cours' => 'En cours',
                                        'prete' => 'Prête'
                                    ];
                                @endphp
                                <span class="badge" style="{{ $prepaBadges[$commande->statut_preparation] ?? '' }}">
                                    {{ $prepaLabels[$commande->statut_preparation] ?? $commande->statut_preparation }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $livraisonBadges = [
                                        'en_attente' => 'background-color: #F5F5F5; color: #666;',
                                        'en_cours' => 'background-color: #E3F2FD; color: #1976D2;',
                                        'livree' => 'background-color: #E8F5E9; color: #2d9248;',
                                        'echec' => 'background-color: #FFEBEE; color: #C62828;'
                                    ];
                                    $livraisonLabels = [
                                        'en_attente' => 'En attente',
                                        'en_cours' => 'En cours',
                                        'livree' => 'Livrée',
                                        'echec' => 'Échec'
                                    ];
                                @endphp
                                <span class="badge" style="{{ $livraisonBadges[$commande->statut_livraison] ?? '' }}">
                                    {{ $livraisonLabels[$commande->statut_livraison] ?? $commande->statut_livraison }}
                                </span>
                            </td>
                            <td>
                                @if($commande->livraison && $commande->livraison->livreur)
                                    <div>{{ $commande->livraison->livreur->name }}</div>
                                @else
                                    <button onclick="showAffecterModal({{ $commande->id }})" style="padding: 0.25rem 0.5rem; background: #E3F2FD; color: #1976D2; border: none; border-radius: 4px; cursor: pointer; font-size: 0.75rem; font-weight: 600;">
                                        Affecter
                                    </button>
                                @endif
                            </td>
                            <td>{{ $commande->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="{{ route('admin.commandes.show', $commande->id) }}" style="padding: 0.25rem 0.75rem; background-color: #D9542A; color: white; border-radius: 4px; text-decoration: none; font-size: 0.75rem; font-weight: 600;">
                                        Voir
                                    </a>
                                    @if($commande->statut_commande === 'en_attente')
                                    <form method="POST" action="{{ route('admin.commandes.valider', $commande->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" style="padding: 0.25rem 0.75rem; background-color: #10B981; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.75rem; font-weight: 600;">
                                            Valider
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Modal affecter livreur -->
                        <div id="affecterModal{{ $commande->id }}" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
                            <div style="background: white; border-radius: 12px; padding: 2rem; width: 90%; max-width: 500px;">
                                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem; color: #3A3A3A;">Affecter un livreur</h3>
                                <form method="POST" action="{{ route('admin.commandes.affecter-livreur', $commande->id) }}">
                                    @csrf
                                    <div style="margin-bottom: 1rem;">
                                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #3A3A3A;">Sélectionner un livreur</label>
                                        <select name="livreur_id" required style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 6px;">
                                            <option value="">-- Choisir --</option>
                                            @foreach($livreurs as $livreur)
                                            <option value="{{ $livreur->id }}">{{ $livreur->name }} - {{ $livreur->telephone }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                                        <button type="button" onclick="hideAffecterModal({{ $commande->id }})" style="padding: 0.5rem 1rem; background: #F5F5F5; color: #666; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                                            Annuler
                                        </button>
                                        <button type="submit" style="padding: 0.5rem 1rem; background: #D9542A; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                                            Affecter
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 3rem; color: #999;">
                                <div style="font-size: 3rem; margin-bottom: 1rem;">📦</div>
                                <p>Aucune commande trouvée</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    {{ $commandes->links('vendor.pagination.domini') }}
@endsection

@section('scripts')
<script>
function showAffecterModal(id) {
    document.getElementById('affecterModal' + id).style.display = 'flex';
}

function hideAffecterModal(id) {
    document.getElementById('affecterModal' + id).style.display = 'none';
}
</script>
@endsection
