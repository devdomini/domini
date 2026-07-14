@extends('admin.layout')

@section('title', 'Commandes')
@section('page-title', 'Gestion des Commandes')

@section('content')
    <!-- Actions Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #000000;">Commandes</h2>
            <p style="color: #666; margin-top: 0.25rem;">Choisissez l’affichage ci-dessous. Les filtres (client, dates, statuts) s’appliquent ensuite.</p>
        </div>
    </div>

    @php
        $navQs = request()->except(['page', 'page_classique', 'page_lot', 'type_commande']);
        $curType = (string) ($typeVue ?? '');
    @endphp
    <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;">
        <span style="font-size: 0.8rem; font-weight: 700; color: #666; text-transform: uppercase; letter-spacing: 0.03em; margin-right: 0.25rem;">Affichage</span>
        <a href="{{ route('admin.commandes.index', $navQs) }}" style="padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.9rem; text-decoration: none; border: 2px solid {{ $curType === '' ? '#FF0000' : '#E5E5E5' }}; background: {{ $curType === '' ? '#FF0000' : 'white' }}; color: {{ $curType === '' ? 'white' : '#374151' }};">Les deux</a>
        <a href="{{ route('admin.commandes.index', array_merge($navQs, ['type_commande' => 'classique'])) }}" style="padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.9rem; text-decoration: none; border: 2px solid {{ $curType === 'classique' ? '#2E7D32' : '#E5E5E5' }}; background: {{ $curType === 'classique' ? '#2E7D32' : 'white' }}; color: {{ $curType === 'classique' ? 'white' : '#374151' }};">Classiques</a>
        <a href="{{ route('admin.commandes.index', array_merge($navQs, ['type_commande' => 'lot'])) }}" style="padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.9rem; text-decoration: none; border: 2px solid {{ $curType === 'lot' ? '#3949AB' : '#E5E5E5' }}; background: {{ $curType === 'lot' ? '#3949AB' : 'white' }}; color: {{ $curType === 'lot' ? 'white' : '#374151' }};">Lots entreprise</a>
    </div>

    <!-- Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div style="background: linear-gradient(135deg, #FF0000, #CC0000); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Total Commandes</div>
            <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ \App\Models\Commande::count() }}</div>
        </div>
        <div style="background: linear-gradient(135deg, #E8F5E9, #c8e6c9); border-radius: 12px; padding: 1.5rem; color: #1B5E20;">
            <div style="font-size: 0.875rem; opacity: 0.85;">Classiques</div>
            <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ \App\Models\Commande::where('is_lunch', false)->count() }}</div>
        </div>
        <div style="background: linear-gradient(135deg, #E8EAF6, #c5cae9); border-radius: 12px; padding: 1.5rem; color: #283593;">
            <div style="font-size: 0.875rem; opacity: 0.85;">Lots entreprise</div>
            <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ \App\Models\Commande::where('is_lunch', true)->count() }}</div>
        </div>
        <div style="background: linear-gradient(135deg, #CC0000, #990000); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">En attente</div>
            <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ \App\Models\Commande::where('statut_commande', 'en_attente')->count() }}</div>
        </div>
    </div>

    @if(session('success'))
    <div style="background-color: #E8F5E9; color: #2d9248; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
        {{ session('success') }}
    </div>
    @endif

    @php
        $showTypeBadge = ($typeVue ?? '') === '';
    @endphp

    <!-- Filters -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <form method="GET" action="{{ route('admin.commandes.index') }}" style="padding: 1rem; display: grid; gap: 1rem;">
            <input type="hidden" name="type_commande" value="{{ $curType }}">
            <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #666; margin-bottom: 0.35rem;">Client (nom, e-mail, tél., réf.)</label>
                    <input type="text" name="client" value="{{ request('client') }}" placeholder="Rechercher…" style="width: 100%; padding: 0.5rem 0.65rem; border: 2px solid #E5E5E5; border-radius: 6px; box-sizing: border-box;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #666; margin-bottom: 0.35rem;">Du</label>
                    <input type="date" name="date_debut" value="{{ request('date_debut') }}" style="padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #666; margin-bottom: 0.35rem;">Au</label>
                    <input type="date" name="date_fin" value="{{ request('date_fin') }}" style="padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px;">
                </div>
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
                <select name="statut_commande" style="padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px; background: white;">
                    <option value="">Statut commande</option>
                    <option value="en_attente" @selected(request('statut_commande') === 'en_attente')>En attente</option>
                    <option value="confirmee" @selected(request('statut_commande') === 'confirmee')>Confirmée</option>
                    <option value="annulee" @selected(request('statut_commande') === 'annulee')>Annulée</option>
                    <option value="terminee" @selected(request('statut_commande') === 'terminee')>Terminée</option>
                </select>
                <select name="statut_preparation" style="padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px; background: white;">
                    <option value="">Statut préparation</option>
                    <option value="en_attente" @selected(request('statut_preparation') === 'en_attente')>En attente</option>
                    <option value="en_cours" @selected(request('statut_preparation') === 'en_cours')>En cours</option>
                    <option value="prete" @selected(request('statut_preparation') === 'prete')>Prête</option>
                </select>
                <select name="statut_livraison" style="padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px; background: white;">
                    <option value="">Statut livraison</option>
                    <option value="en_attente" @selected(request('statut_livraison') === 'en_attente')>En attente</option>
                    <option value="en_cours" @selected(request('statut_livraison') === 'en_cours')>En cours</option>
                    <option value="livree" @selected(request('statut_livraison') === 'livree')>Livrée</option>
                    <option value="echec" @selected(request('statut_livraison') === 'echec')>Échec</option>
                </select>
                <button type="submit" style="padding: 0.5rem 1.25rem; background: #FF0000; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                    Filtrer
                </button>
                <a href="{{ route('admin.commandes.index', $curType !== '' ? ['type_commande' => $curType] : []) }}" style="padding: 0.5rem 1rem; color: #666; font-weight: 600; text-decoration: none;">Réinitialiser les filtres</a>
            </div>
        </form>
    </div>

    @php
        $commandesPourModals = collect();
        if ($commandesClassiques) {
            $commandesPourModals = $commandesPourModals->merge($commandesClassiques->items());
        }
        if ($commandesLots) {
            $commandesPourModals = $commandesPourModals->merge($commandesLots->items());
        }
        if (isset($commandesLotsGrouped) && $commandesLotsGrouped) {
            foreach ($commandesLotsGrouped as $bloc) {
                $commandesPourModals = $commandesPourModals->merge($bloc['commandes']);
            }
        }
    @endphp

    @if($commandesClassiques)
    <div style="margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem; flex-wrap: wrap;">
            <h3 style="font-size: 1.15rem; font-weight: 800; color: #2E7D32; margin: 0;">Commandes classiques</h3>
            <span style="font-size: 0.8rem; color: #666;">({{ $commandesClassiques->total() }} résultat(s))</span>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Client</th>
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
                            @include('admin.commandes._rows', ['commandes' => $commandesClassiques, 'showTypeBadge' => $showTypeBadge])
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{ $commandesClassiques->links('vendor.pagination.domini') }}
    </div>
    @endif

    @if($commandesLots)
    <div style="margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem; flex-wrap: wrap;">
            <h3 style="font-size: 1.15rem; font-weight: 800; color: #3949AB; margin: 0;">Commandes lot (entreprise)</h3>
            <span style="font-size: 0.8rem; color: #666;">({{ $commandesLots->total() }} commande(s), liste individuelle)</span>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Client</th>
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
                            @include('admin.commandes._rows', ['commandes' => $commandesLots, 'showTypeBadge' => $showTypeBadge])
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{ $commandesLots->links('vendor.pagination.domini') }}
    </div>
    @endif

    @if(isset($commandesLotsGrouped) && $commandesLotsGrouped)
    <div style="margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem; flex-wrap: wrap;">
            <h3 style="font-size: 1.15rem; font-weight: 800; color: #3949AB; margin: 0;">Lots entreprise</h3>
            <span style="font-size: 0.8rem; color: #666;">
                @if($lotsFiltreesCount !== null)
                    {{ $lotsFiltreesCount }} commande(s) · {{ $commandesLotsGrouped->total() }} entreprise(s)
                @else
                    {{ $commandesLotsGrouped->total() }} entreprise(s)
                @endif
            </span>
        </div>
        <p style="font-size: 0.85rem; color: #666; margin: 0 0 1rem 0;">Regroupement par entreprise. Cliquez sur l’en-tête d’une carte pour la réduire ou l’agrandir. Pagination : nombre d’entreprises par page.</p>

        <style>
            .ec-lot-card > summary { list-style: none; cursor: pointer; }
            .ec-lot-card > summary::-webkit-details-marker { display: none; }
            .ec-lot-card > summary::marker { content: ''; }
        </style>

        @forelse($commandesLotsGrouped as $bloc)
        <details class="ec-lot-card" open style="margin-bottom: 1.5rem; border: 1px solid #c5cae9; border-radius: 10px; overflow: hidden;">
            <summary style="display: flex; justify-content: space-between; align-items: center; gap: 0.75rem; flex-wrap: wrap; background: linear-gradient(135deg, #E8EAF6, #dce1f6); padding: 0.65rem 1rem;">
                <div style="flex: 1; min-width: 200px;">
                    <strong style="color: #283593;">{{ $bloc['entreprise']?->nom ?? 'Sans entreprise' }}</strong>
                    @if($bloc['entreprise'] && ($bloc['entreprise']->ville || $bloc['entreprise']->adresse))
                        <span style="color: #555; font-size: 0.875rem;"> — {{ $bloc['entreprise']->ville ?? '' }}{{ $bloc['entreprise']->ville && $bloc['entreprise']->adresse ? ' · ' : '' }}{{ \Illuminate\Support\Str::limit($bloc['entreprise']->adresse ?? '', 80) }}</span>
                    @endif
                    <span style="color: #666; font-size: 0.8rem; margin-left: 0.35rem;">({{ $bloc['commandes']->count() }} commande(s))</span>
                </div>
                <span style="font-size: 0.75rem; font-weight: 700; color: #3949AB; white-space: nowrap; user-select: none;">▼ Réduire / ▶ Agrandir</span>
            </summary>
            <div class="card" style="border: none; border-radius: 0; margin: 0; box-shadow: none;">
                <div class="card-body" style="padding-top: 0;">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Client</th>
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
                                @include('admin.commandes._rows', ['commandes' => $bloc['commandes'], 'showTypeBadge' => false])
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </details>
        @empty
        <div class="card" style="padding: 2rem; text-align: center; color: #999;">
            Aucune commande lot ne correspond aux filtres.
        </div>
        @endforelse

        {{ $commandesLotsGrouped->links('vendor.pagination.domini') }}
    </div>
    @endif

    @include('admin.commandes._livreur_modals', ['commandes' => $commandesPourModals, 'livreurs' => $livreurs])
@endsection

@section('scripts')
<script>
function showAffecterModal(id) {
    var el = document.getElementById('affecterModal' + id);
    if (el) el.style.display = 'flex';
}

function hideAffecterModal(id) {
    var el = document.getElementById('affecterModal' + id);
    if (el) el.style.display = 'none';
}
</script>
@endsection
