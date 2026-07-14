@extends('admin.layout')

@section('title', 'Livraisons')
@section('page-title', 'Gestion des Livraisons')

@section('content')
    <!-- Actions Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #000000;">Toutes les livraisons</h2>
            <p style="color: #666; margin-top: 0.25rem;">Suivez les livraisons en temps réel</p>
        </div>
    </div>

    <!-- Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div style="background: linear-gradient(135deg, #FF0000, #CC0000); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Total Livraisons</div>
            <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ \App\Models\Livraison::count() }}</div>
        </div>
        <div style="background: linear-gradient(135deg, #CC0000, #990000); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">En Cours</div>
            <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ \App\Models\Livraison::whereIn('statut', ['assignee', 'en_cours'])->count() }}</div>
        </div>
        <div style="background: linear-gradient(135deg, #10B981, #059669); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Livrées</div>
            <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ \App\Models\Livraison::where('statut', 'livree')->count() }}</div>
        </div>
        <div style="background: linear-gradient(135deg, #1A1A1A, #000000); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Échecs</div>
            <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ \App\Models\Livraison::where('statut', 'echec')->count() }}</div>
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
            <select name="statut" style="padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px; background: white;">
                <option value="">Tous les statuts</option>
                <option value="en_attente">En attente</option>
                <option value="assignee">Assignée</option>
                <option value="en_cours">En cours</option>
                <option value="livree">Livrée</option>
                <option value="echec">Échec</option>
            </select>
            
            <select name="livreur_id" style="padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px; background: white;">
                <option value="">Tous les livreurs</option>
                @foreach(\App\Models\User::where('role', 'livreur')->get() as $livreur)
                <option value="{{ $livreur->id }}">{{ $livreur->name }}</option>
                @endforeach
            </select>
            
            <button type="submit" style="padding: 0.5rem 1rem; background: #FF0000; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                Filtrer
            </button>
        </form>
    </div>

    <!-- Livraisons Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Commande</th>
                            <th>Client</th>
                            <th>Livreur</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Horaires</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($livraisons as $livraison)
                        <tr>
                            <td>
                                <div style="font-weight: 600;">{{ $livraison->commande->ref }}</div>
                                <div style="font-size: 0.75rem; color: #999;">{{ $livraison->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td>
                                <div>{{ $livraison->client->name ?? 'N/A' }}</div>
                                <div style="font-size: 0.75rem; color: #999;">{{ $livraison->client->telephone ?? '' }}</div>
                            </td>
                            <td>
                                @if($livraison->livreur)
                                <div>{{ $livraison->livreur->name }}</div>
                                <div style="font-size: 0.75rem; color: #999;">{{ $livraison->livreur->telephone }}</div>
                                @else
                                <span style="color: #999;">Non assigné</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #FF0000;">{{ number_format($livraison->commande->montant_total, 0, ',', ' ') }} FCFA</div>
                                <div style="font-size: 0.75rem; color: #999;">Livraison: {{ number_format($livraison->montant_livraison, 0, ',', ' ') }} FCFA</div>
                            </td>
                            <td>
                                @php
                                    $badges = [
                                        'en_attente' => 'background-color: #F5F5F5; color: #666;',
                                        'assignee' => 'background-color: #FFF3E0; color: #E65100;',
                                        'en_cours' => 'background-color: #E3F2FD; color: #1976D2;',
                                        'livree' => 'background-color: #E8F5E9; color: #2d9248;',
                                        'echec' => 'background-color: #FFEBEE; color: #CC0000;'
                                    ];
                                    $labels = [
                                        'en_attente' => 'En attente',
                                        'assignee' => 'Assignée',
                                        'en_cours' => 'En cours',
                                        'livree' => 'Livrée',
                                        'echec' => 'Échec'
                                    ];
                                @endphp
                                <span class="badge" style="{{ $badges[$livraison->statut] ?? '' }}">
                                    {{ $labels[$livraison->statut] ?? $livraison->statut }}
                                </span>
                            </td>
                            <td>
                                @if($livraison->heure_assignation)
                                <div style="font-size: 0.75rem; display: flex; align-items: center; gap: 0.25rem;">
                                    @include('admin.partials.icon', ['name' => 'clock', 'size' => 12]) {{ $livraison->heure_assignation->format('H:i') }}
                                </div>
                                @endif
                                @if($livraison->heure_prise_en_charge)
                                <div style="font-size: 0.75rem; display: flex; align-items: center; gap: 0.25rem;">
                                    @include('admin.partials.icon', ['name' => 'package', 'size' => 12]) {{ $livraison->heure_prise_en_charge->format('H:i') }}
                                </div>
                                @endif
                                @if($livraison->heure_livraison)
                                <div style="font-size: 0.75rem; display: flex; align-items: center; gap: 0.25rem;">
                                    @include('admin.partials.icon', ['name' => 'check-circle', 'size' => 12]) {{ $livraison->heure_livraison->format('H:i') }}
                                </div>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="{{ route('admin.livraisons.show', $livraison->id) }}" style="padding: 0.25rem 0.75rem; background-color: #FF0000; color: white; border-radius: 4px; text-decoration: none; font-size: 0.75rem; font-weight: 600;">
                                        Voir
                                    </a>
                                    @if($livraison->statut !== 'livree' && $livraison->statut !== 'echec')
                                    <button onclick="showChangerStatutModal({{ $livraison->id }})" style="padding: 0.25rem 0.75rem; background-color: #CC0000; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.75rem; font-weight: 600;">
                                        Statut
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Modal changer statut -->
                        <div id="changerStatutModal{{ $livraison->id }}" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
                            <div style="background: white; border-radius: 12px; padding: 2rem; width: 90%; max-width: 500px;">
                                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem; color: #000000;">Changer le statut</h3>
                                <form method="POST" action="{{ route('admin.livraisons.changer-statut', $livraison->id) }}">
                                    @csrf
                                    <div style="margin-bottom: 1rem;">
                                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #000000;">Nouveau statut</label>
                                        <select name="statut" required style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 6px;">
                                            <option value="en_attente" {{ $livraison->statut === 'en_attente' ? 'selected' : '' }}>En attente</option>
                                            <option value="assignee" {{ $livraison->statut === 'assignee' ? 'selected' : '' }}>Assignée</option>
                                            <option value="en_cours" {{ $livraison->statut === 'en_cours' ? 'selected' : '' }}>En cours</option>
                                            <option value="livree" {{ $livraison->statut === 'livree' ? 'selected' : '' }}>Livrée</option>
                                            <option value="echec" {{ $livraison->statut === 'echec' ? 'selected' : '' }}>Échec</option>
                                        </select>
                                    </div>
                                    <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                                        <button type="button" onclick="hideChangerStatutModal({{ $livraison->id }})" style="padding: 0.5rem 1rem; background: #F5F5F5; color: #666; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                                            Annuler
                                        </button>
                                        <button type="submit" style="padding: 0.5rem 1rem; background: #FF0000; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                                            Mettre à jour
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: #999;">
                                <div style="margin-bottom: 1rem; color: #999; display: flex; justify-content: center;">
                                    @include('admin.partials.icon', ['name' => 'truck', 'size' => 48])
                                </div>
                                <p>Aucune livraison trouvée</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    {{ $livraisons->links('vendor.pagination.domini') }}
@endsection

@section('scripts')
<script>
function showChangerStatutModal(id) {
    document.getElementById('changerStatutModal' + id).style.display = 'flex';
}

function hideChangerStatutModal(id) {
    document.getElementById('changerStatutModal' + id).style.display = 'none';
}
</script>
@endsection
