@extends('admin.layout')

@section('title', 'Paiements')
@section('page-title', 'Gestion des Paiements')

@section('content')
    <!-- Actions Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #3A3A3A;">Tous les paiements</h2>
            <p style="color: #666; margin-top: 0.25rem;">Traçabilité complète des transactions</p>
        </div>
    </div>

    <!-- Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div style="background: linear-gradient(135deg, #10B981, #059669); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Total Validé</div>
            <div style="font-size: 1.5rem; font-weight: 900; margin: 0.5rem 0;">{{ number_format($stats['total'], 0, ',', ' ') }} FCFA</div>
        </div>
        <div style="background: linear-gradient(135deg, #F7B801, #e5a900); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">En Attente</div>
            <div style="font-size: 1.5rem; font-weight: 900; margin: 0.5rem 0;">{{ number_format($stats['en_attente'], 0, ',', ' ') }} FCFA</div>
        </div>
        <div style="background: linear-gradient(135deg, #3B82F6, #2563EB); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Remboursé</div>
            <div style="font-size: 1.5rem; font-weight: 900; margin: 0.5rem 0;">{{ number_format($stats['rembourse'], 0, ',', ' ') }} FCFA</div>
        </div>
        <div style="background: linear-gradient(135deg, #3A3A3A, #2A2A2A); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Total Transactions</div>
            <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ \App\Models\Paiement::count() }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <form method="GET" style="padding: 1rem; display: flex; gap: 1rem; flex-wrap: wrap;">
            <select name="type" style="padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px; background: white;">
                <option value="">Tous les types</option>
                <option value="abonnement">Abonnement</option>
                <option value="commande">Commande</option>
                <option value="remboursement">Remboursement</option>
                <option value="paiement_livraison">Paiement livraison</option>
            </select>
            
            <select name="statut" style="padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px; background: white;">
                <option value="">Tous les statuts</option>
                <option value="en_attente">En attente</option>
                <option value="valide">Validé</option>
                <option value="echoue">Échoué</option>
                <option value="rembourse">Remboursé</option>
            </select>

            <select name="mode_paiement" style="padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px; background: white;">
                <option value="">Tous les modes</option>
                <option value="especes">Espèces</option>
                <option value="carte">Carte bancaire</option>
                <option value="mobile_money">Mobile Money</option>
                <option value="wallet">Wallet</option>
                <option value="virement">Virement</option>
            </select>
            
            <button type="submit" style="padding: 0.5rem 1rem; background: #D9542A; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                Filtrer
            </button>
        </form>
    </div>

    <!-- Paiements Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Type</th>
                            <th>Utilisateur</th>
                            <th>Commande</th>
                            <th>Montant</th>
                            <th>Mode</th>
                            <th>Statut</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paiements as $paiement)
                        <tr>
                            <td>
                                <div style="font-weight: 600;">{{ $paiement->ref }}</div>
                            </td>
                            <td>
                                @php
                                    $typeBadges = [
                                        'abonnement' => 'background-color: #E3F2FD; color: #1976D2;',
                                        'commande' => 'background-color: #FFF3E0; color: #E65100;',
                                        'remboursement' => 'background-color: #FFF9C4; color: #F57F17;',
                                        'paiement_livraison' => 'background-color: #E8F5E9; color: #2d9248;'
                                    ];
                                    $typeLabels = [
                                        'abonnement' => 'Abonnement',
                                        'commande' => 'Commande',
                                        'remboursement' => 'Remboursement',
                                        'paiement_livraison' => 'Paiement livraison'
                                    ];
                                @endphp
                                <span class="badge" style="{{ $typeBadges[$paiement->type] ?? '' }}">
                                    {{ $typeLabels[$paiement->type] ?? $paiement->type }}
                                </span>
                            </td>
                            <td>
                                @if($paiement->user)
                                <div>{{ $paiement->user->name }}</div>
                                <div style="font-size: 0.75rem; color: #999;">{{ $paiement->user->email }}</div>
                                @else
                                <span style="color: #999;">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($paiement->commande)
                                <a href="{{ route('admin.commandes.show', $paiement->commande->id) }}" style="color: #D9542A; text-decoration: none; font-weight: 600;">
                                    {{ $paiement->commande->ref }}
                                </a>
                                @else
                                <span style="color: #999;">-</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-weight: 600; color: {{ $paiement->type === 'remboursement' ? '#EF4444' : '#10B981' }};">
                                    {{ $paiement->type === 'remboursement' ? '-' : '' }}{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA
                                </span>
                            </td>
                            <td>
                                @php
                                    $modeLabels = [
                                        'especes' => 'Espèces',
                                        'carte' => 'Carte bancaire',
                                        'mobile_money' => 'Mobile Money',
                                        'wallet' => 'Wallet',
                                        'virement' => 'Virement'
                                    ];
                                @endphp
                                {{ $modeLabels[$paiement->mode_paiement] ?? $paiement->mode_paiement }}
                            </td>
                            <td>
                                @php
                                    $statutBadges = [
                                        'en_attente' => 'background-color: #FFF3E0; color: #E65100;',
                                        'valide' => 'background-color: #E8F5E9; color: #2d9248;',
                                        'echoue' => 'background-color: #FFEBEE; color: #C62828;',
                                        'rembourse' => 'background-color: #E3F2FD; color: #1976D2;'
                                    ];
                                    $statutLabels = [
                                        'en_attente' => 'En attente',
                                        'valide' => 'Validé',
                                        'echoue' => 'Échoué',
                                        'rembourse' => 'Remboursé'
                                    ];
                                @endphp
                                <span class="badge" style="{{ $statutBadges[$paiement->statut] ?? '' }}">
                                    {{ $statutLabels[$paiement->statut] ?? $paiement->statut }}
                                </span>
                            </td>
                            <td>
                                <div>{{ $paiement->created_at->format('d/m/Y') }}</div>
                                <div style="font-size: 0.75rem; color: #999;">{{ $paiement->created_at->format('H:i') }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3rem; color: #999;">
                                <div style="font-size: 3rem; margin-bottom: 1rem;">💰</div>
                                <p>Aucun paiement trouvé</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    {{ $paiements->links('vendor.pagination.domini') }}
@endsection
