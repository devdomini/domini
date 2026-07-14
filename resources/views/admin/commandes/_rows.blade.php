@forelse($commandes as $commande)
<tr>
    <td>
        <div style="font-weight: 600;">{{ $commande->ref }}</div>
        @if(!empty($showTypeBadge))
        <div style="margin-top: 0.25rem;">
            @if($commande->is_lunch)
            <span style="font-size: 0.65rem; font-weight: 700; padding: 0.15rem 0.4rem; border-radius: 4px; background: #E8EAF6; color: #3949AB;">Lot entreprise</span>
            @else
            <span style="font-size: 0.65rem; font-weight: 700; padding: 0.15rem 0.4rem; border-radius: 4px; background: #E8F5E9; color: #2E7D32;">Classique</span>
            @endif
        </div>
        @endif
    </td>
    <td>
        <div>{{ $commande->employe->name ?? 'N/A' }}</div>
        <div style="font-size: 0.75rem; color: #999;">{{ $commande->employe->email ?? '' }}</div>
    </td>
    <td>
        <span style="font-weight: 600; color: #FF0000;">{{ number_format($commande->montant_total, 0, ',', ' ') }} FCFA</span>
    </td>
    <td>
        @php
            $badges = [
                'en_attente' => 'background-color: #FFF3E0; color: #E65100;',
                'confirmee' => 'background-color: #E8F5E9; color: #2d9248;',
                'annulee' => 'background-color: #FFEBEE; color: #CC0000;',
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
                'echec' => 'background-color: #FFEBEE; color: #CC0000;'
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
            <button type="button" onclick="showAffecterModal({{ $commande->id }})" style="padding: 0.25rem 0.5rem; background: #E3F2FD; color: #1976D2; border: none; border-radius: 4px; cursor: pointer; font-size: 0.75rem; font-weight: 600;">
                Affecter
            </button>
        @endif
    </td>
    <td>{{ $commande->created_at->format('d/m/Y H:i') }}</td>
    <td>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.commandes.show', $commande->id) }}" style="padding: 0.25rem 0.75rem; background-color: #FF0000; color: white; border-radius: 4px; text-decoration: none; font-size: 0.75rem; font-weight: 600;">
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
@empty
<tr>
    <td colspan="9" style="text-align: center; padding: 2rem; color: #999;">
        <p style="margin: 0;">Aucune commande dans cette section</p>
    </td>
</tr>
@endforelse
