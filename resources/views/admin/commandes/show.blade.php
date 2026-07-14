@extends('admin.layout')

@section('title', 'Détails Commande')
@section('page-title', 'Détails de la Commande')

@section('content')
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
        <a href="{{ route('admin.commandes.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: #666; text-decoration: none; margin-bottom: 1rem;">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour aux commandes
        </a>
        <h2 style="font-size: 1.5rem; font-weight: 700; color: #000000;">Commande {{ $commande->ref }}</h2>
        <p style="color: #666; margin-top: 0.25rem;">Créée le {{ $commande->created_at->format('d/m/Y à H:i') }}</p>
    </div>

    @if(session('success'))
    <div style="background-color: #E8F5E9; color: #2d9248; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
        {{ session('success') }}
    </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 1.5rem;">
        <!-- Main Content -->
        <div>
            <!-- Statuts -->
            <div class="card" style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #000000; margin-bottom: 1.5rem;">Statuts de la commande</h3>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: #666;">Statut commande</label>
                        <form method="POST" action="{{ route('admin.commandes.changer-statut-commande', $commande->id) }}">
                            @csrf
                            <select name="statut_commande" onchange="this.form.submit()" style="width: 100%; padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px; background: white;">
                                <option value="en_attente" {{ $commande->statut_commande === 'en_attente' ? 'selected' : '' }}>En attente</option>
                                <option value="confirmee" {{ $commande->statut_commande === 'confirmee' ? 'selected' : '' }}>Confirmée</option>
                                <option value="annulee" {{ $commande->statut_commande === 'annulee' ? 'selected' : '' }}>Annulée</option>
                                <option value="terminee" {{ $commande->statut_commande === 'terminee' ? 'selected' : '' }}>Terminée</option>
                            </select>
                        </form>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: #666;">Statut préparation</label>
                        <form method="POST" action="{{ route('admin.commandes.changer-statut-preparation', $commande->id) }}">
                            @csrf
                            <select name="statut_preparation" onchange="this.form.submit()" style="width: 100%; padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px; background: white;">
                                <option value="en_attente" {{ $commande->statut_preparation === 'en_attente' ? 'selected' : '' }}>En attente</option>
                                <option value="en_cours" {{ $commande->statut_preparation === 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="prete" {{ $commande->statut_preparation === 'prete' ? 'selected' : '' }}>Prête</option>
                            </select>
                        </form>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: #666;">Statut livraison</label>
                        <form method="POST" action="{{ route('admin.commandes.changer-statut-livraison', $commande->id) }}">
                            @csrf
                            <select name="statut_livraison" onchange="this.form.submit()" style="width: 100%; padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px; background: white;">
                                <option value="en_attente" {{ $commande->statut_livraison === 'en_attente' ? 'selected' : '' }}>En attente</option>
                                <option value="en_cours" {{ $commande->statut_livraison === 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="livree" {{ $commande->statut_livraison === 'livree' ? 'selected' : '' }}>Livrée</option>
                                <option value="echec" {{ $commande->statut_livraison === 'echec' ? 'selected' : '' }}>Échec</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Articles -->
            <div class="card" style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #000000; margin-bottom: 1.5rem;">Articles commandés</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Plat</th>
                                <th>Prix unitaire</th>
                                <th>Quantité</th>
                                <th>Subventionné</th>
                                <th style="text-align: right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($commande->items as $item)
                            <tr>
                                <td>
                                    <div style="font-weight: 600;">{{ $item->plat->nom ?? 'N/A' }}</div>
                                    @if(is_array($item->accompagnements) && !empty($item->accompagnements))
                                    <div style="font-size: 0.75rem; color: #999; margin-top: 0.25rem;">
                                        <strong>Accompagnements:</strong> {{ implode(', ', array_column($item->accompagnements, 'nom')) }}
                                    </div>
                                    @endif
                                    @if(is_array($item->options) && !empty($item->options))
                                    <div style="font-size: 0.75rem; color: #999;">
                                        <strong>Options:</strong> {{ implode(', ', array_column($item->options, 'nom')) }}
                                    </div>
                                    @endif
                                </td>
                                <td>{{ number_format($item->prix, 0, ',', ' ') }} FCFA</td>
                                <td>{{ $item->quantite }}</td>
                                <td>
                                    @if($item->is_subventionne)
                                        <span class="badge" style="background-color: #E8F5E9; color: #2d9248;">Oui</span>
                                    @else
                                        <span class="badge" style="background-color: #F5F5F5; color: #666;">Non</span>
                                    @endif
                                </td>
                                <td style="text-align: right; font-weight: 600;">{{ number_format($item->prix * $item->quantite, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            @endforeach
                            <tr style="background: #F9FAFB;">
                                <td colspan="4" style="text-align: right; font-weight: 600;">Total commande:</td>
                                <td style="text-align: right; font-weight: 700; color: #FF0000; font-size: 1.125rem;">
                                    {{ number_format($commande->montant_total, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Consignes -->
            <div class="card">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #000000; margin-bottom: 1.5rem;">Consignes</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: #666;">Pour le cuisinier</label>
                        <div style="padding: 1rem; background: #F9FAFB; border-radius: 6px; border: 1px solid #E5E5E5;">
                            {{ $commande->consigne_cuisinier ?? 'Aucune consigne' }}
                        </div>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: #666;">Pour le livreur</label>
                        <div style="padding: 1rem; background: #F9FAFB; border-radius: 6px; border: 1px solid #E5E5E5;">
                            {{ $commande->consigne_livreur ?? 'Aucune consigne' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Client -->
            <div class="card" style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #000000; margin-bottom: 1rem;">Client</h3>
                <div style="display: grid; gap: 0.75rem;">
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <svg style="width: 16px; height: 16px; color: #FF0000;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <strong>{{ $commande->employe->name ?? 'N/A' }}</strong>
                    </div>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <svg style="width: 16px; height: 16px; color: #CC0000;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span style="font-size: 0.875rem; color: #666;">{{ $commande->employe->email ?? 'N/A' }}</span>
                    </div>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <svg style="width: 16px; height: 16px; color: #10B981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span style="font-size: 0.875rem; color: #666;">{{ $commande->numero_telephone ?? $commande->employe->telephone ?? 'N/A' }}</span>
                    </div>
                    @if($commande->employe && $commande->employe->entreprise)
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <svg style="width: 16px; height: 16px; color: #000000;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span style="font-size: 0.875rem; color: #666;">{{ $commande->employe->entreprise->nom }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Livraison -->
            <div class="card" style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #000000; margin-bottom: 1rem;">Livraison</h3>
                @if($commande->livraison && $commande->livraison->livreur)
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: #666;">Livreur assigné</label>
                        <div style="font-weight: 600;">{{ $commande->livraison->livreur->name }}</div>
                        <div style="font-size: 0.875rem; color: #999;">{{ $commande->livraison->livreur->telephone }}</div>
                    </div>
                    @if($commande->livraison->heure_assignation)
                    <div style="font-size: 0.875rem; color: #666; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.375rem;">
                        @include('admin.partials.icon', ['name' => 'clock', 'size' => 14])
                        Assignée: {{ $commande->livraison->heure_assignation->format('d/m/Y H:i') }}
                    </div>
                    @endif
                @else
                    <p style="color: #999; margin-bottom: 1rem;">Aucun livreur assigné</p>
                    <button onclick="showAffecterModal()" style="width: 100%; padding: 0.75rem; background: #FF0000; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                        Affecter un livreur
                    </button>
                @endif

                @if($commande->lieu)
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #E5E5E5;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: #666;">Lieu de livraison</label>
                    <div style="margin-bottom: 0.5rem;">{{ $commande->lieu }}</div>
                    @if($commande->lat && $commande->long)
                    <a href="https://www.google.com/maps?q={{ $commande->lat }},{{ $commande->long }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: #E3F2FD; color: #1976D2; text-decoration: none; border-radius: 6px; font-size: 0.875rem; font-weight: 600;">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Voir sur la carte
                    </a>
                    @endif
                </div>
                @endif
            </div>

            <!-- Paiement -->
            <div class="card">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #000000; margin-bottom: 1rem;">Paiement</h3>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: #666;">Statut</label>
                    @php
                        $paymentBadges = [
                            'en_attente' => 'background-color: #FFF3E0; color: #E65100;',
                            'paye' => 'background-color: #E8F5E9; color: #2d9248;',
                            'rembourse' => 'background-color: #E3F2FD; color: #1976D2;'
                        ];
                    @endphp
                    <span class="badge" style="{{ $paymentBadges[$commande->statut_paiement] ?? '' }}">
                        {{ ucfirst($commande->statut_paiement) }}
                    </span>
                </div>
                @if($commande->mode_paiement)
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: #666;">Mode de paiement</label>
                    <div>{{ ucfirst(str_replace('_', ' ', $commande->mode_paiement)) }}</div>
                </div>
                @endif
                <div style="padding-top: 1rem; border-top: 1px solid #E5E5E5;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: #666;">Montant total</label>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #FF0000;">{{ number_format($commande->montant_total, 0, ',', ' ') }} FCFA</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal affecter livreur -->
    @if(!$commande->livraison || !$commande->livraison->livreur)
    <div id="affecterModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 12px; padding: 2rem; width: 90%; max-width: 500px;">
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem; color: #000000;">Affecter un livreur</h3>
            <form method="POST" action="{{ route('admin.commandes.affecter-livreur', $commande->id) }}">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #000000;">Sélectionner un livreur</label>
                    <select name="livreur_id" required style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 6px;">
                        <option value="">-- Choisir --</option>
                        @foreach($livreurs as $livreur)
                        <option value="{{ $livreur->id }}">{{ $livreur->name }} - {{ $livreur->telephone }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                    <button type="button" onclick="hideAffecterModal()" style="padding: 0.5rem 1rem; background: #F5F5F5; color: #666; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                        Annuler
                    </button>
                    <button type="submit" style="padding: 0.5rem 1rem; background: #FF0000; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                        Affecter
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
@endsection

@section('scripts')
<script>
function showAffecterModal() {
    document.getElementById('affecterModal').style.display = 'flex';
}

function hideAffecterModal() {
    document.getElementById('affecterModal').style.display = 'none';
}
</script>
@endsection
