@extends('admin.layout')

@section('title', 'Détails Livraison')
@section('page-title', 'Détails de la Livraison')

@section('content')
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
        <a href="{{ route('admin.livraisons.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: #666; text-decoration: none; margin-bottom: 1rem;">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour aux livraisons
        </a>
        <h2 style="font-size: 1.5rem; font-weight: 700; color: #000000;">Livraison #{{ $livraison->id }}</h2>
        <p style="color: #666; margin-top: 0.25rem;">Créée le {{ $livraison->created_at->format('d/m/Y à H:i') }}</p>
    </div>

    @if(session('success'))
    <div style="background-color: #E8F5E9; color: #2d9248; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
        {{ session('success') }}
    </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 1.5rem;">
        <!-- Main Content -->
        <div>
            <!-- Statut -->
            <div class="card" style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #000000; margin-bottom: 1rem;">Statut de la livraison</h3>
                <form method="POST" action="{{ route('admin.livraisons.changer-statut', $livraison->id) }}" style="display: grid; grid-template-columns: 1fr auto; gap: 1rem; align-items: end;">
                    @csrf
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: #666;">Statut actuel</label>
                        <select name="statut" style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 6px; background: white;">
                            <option value="en_attente" {{ $livraison->statut === 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="assignee" {{ $livraison->statut === 'assignee' ? 'selected' : '' }}>Assignée</option>
                            <option value="en_cours" {{ $livraison->statut === 'en_cours' ? 'selected' : '' }}>En cours</option>
                            <option value="livree" {{ $livraison->statut === 'livree' ? 'selected' : '' }}>Livrée</option>
                            <option value="echec" {{ $livraison->statut === 'echec' ? 'selected' : '' }}>Échec</option>
                        </select>
                    </div>
                    <button type="submit" style="padding: 0.75rem 1.5rem; background: #FF0000; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                        Mettre à jour
                    </button>
                </form>
            </div>

            <!-- Chronologie -->
            <div class="card" style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #000000; margin-bottom: 1.5rem;">Chronologie</h3>
                <div style="position: relative; padding-left: 2.5rem;">
                    <!-- Line -->
                    <div style="position: absolute; left: 10px; top: 0; bottom: 0; width: 2px; background: #E5E5E5;"></div>
                    
                    <!-- Step 1 -->
                    <div style="position: relative; padding-bottom: 2rem;">
                        <div style="position: absolute; left: -2.5rem; width: 30px; height: 30px; border-radius: 50%; background: {{ $livraison->heure_assignation ? '#10B981' : '#E5E5E5' }}; display: flex; align-items: center; justify-content: center; color: white;">
                            @include('admin.partials.icon', ['name' => 'clock', 'size' => 14])
                        </div>
                        <div>
                            <div style="font-weight: 600; color: #000000;">Livraison assignée</div>
                            @if($livraison->heure_assignation)
                            <div style="font-size: 0.875rem; color: #666;">{{ $livraison->heure_assignation->format('d/m/Y à H:i') }}</div>
                            @else
                            <div style="font-size: 0.875rem; color: #999;">En attente</div>
                            @endif
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div style="position: relative; padding-bottom: 2rem;">
                        <div style="position: absolute; left: -2.5rem; width: 30px; height: 30px; border-radius: 50%; background: {{ $livraison->heure_prise_en_charge ? '#10B981' : '#E5E5E5' }}; display: flex; align-items: center; justify-content: center; color: white;">
                            @include('admin.partials.icon', ['name' => 'package', 'size' => 14])
                        </div>
                        <div>
                            <div style="font-weight: 600; color: #000000;">Prise en charge</div>
                            @if($livraison->heure_prise_en_charge)
                            <div style="font-size: 0.875rem; color: #666;">{{ $livraison->heure_prise_en_charge->format('d/m/Y à H:i') }}</div>
                            @else
                            <div style="font-size: 0.875rem; color: #999;">En attente</div>
                            @endif
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div style="position: relative;">
                        <div style="position: absolute; left: -2.5rem; width: 30px; height: 30px; border-radius: 50%; background: {{ $livraison->heure_livraison ? '#10B981' : '#E5E5E5' }}; display: flex; align-items: center; justify-content: center; color: white;">
                            @include('admin.partials.icon', ['name' => 'check-circle', 'size' => 14])
                        </div>
                        <div>
                            <div style="font-weight: 600; color: #000000;">Livraison effectuée</div>
                            @if($livraison->heure_livraison)
                            <div style="font-size: 0.875rem; color: #666;">{{ $livraison->heure_livraison->format('d/m/Y à H:i') }}</div>
                            @else
                            <div style="font-size: 0.875rem; color: #999;">En attente</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Détails commande -->
            <div class="card" style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #000000; margin-bottom: 1.5rem;">Détails de la commande {{ $livraison->commande->ref }}</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Plat</th>
                                <th>Quantité</th>
                                <th style="text-align: right;">Prix</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($livraison->commande->items as $item)
                            <tr>
                                <td>
                                    <div style="font-weight: 600;">{{ $item->plat->nom ?? 'N/A' }}</div>
                                    @if(is_array($item->accompagnements) && !empty($item->accompagnements))
                                    <div style="font-size: 0.75rem; color: #999;">+ {{ implode(', ', array_column($item->accompagnements, 'nom')) }}</div>
                                    @endif
                                    @if(is_array($item->options) && !empty($item->options))
                                    <div style="font-size: 0.75rem; color: #999;">+ {{ implode(', ', array_column($item->options, 'nom')) }}</div>
                                    @endif
                                </td>
                                <td>{{ $item->quantite }}</td>
                                <td style="text-align: right;">{{ number_format($item->prix * $item->quantite, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Commentaire -->
            @if($livraison->commentaire)
            <div class="card">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #000000; margin-bottom: 1rem;">Commentaire</h3>
                <p style="color: #666;">{{ $livraison->commentaire }}</p>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Livreur -->
            <div class="card" style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #000000; margin-bottom: 1rem;">Livreur</h3>
                @if($livraison->livreur)
                <div style="display: grid; gap: 0.75rem;">
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <svg style="width: 16px; height: 16px; color: #FF0000;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <strong>{{ $livraison->livreur->name }}</strong>
                    </div>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <svg style="width: 16px; height: 16px; color: #CC0000;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span style="font-size: 0.875rem; color: #666;">{{ $livraison->livreur->telephone ?? 'N/A' }}</span>
                    </div>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <svg style="width: 16px; height: 16px; color: #10B981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span style="font-size: 0.875rem; color: #666;">{{ $livraison->livreur->email }}</span>
                    </div>
                </div>
                @else
                <p style="color: #999;">Aucun livreur assigné</p>
                @endif
            </div>

            <!-- Client -->
            <div class="card" style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #000000; margin-bottom: 1rem;">Client</h3>
                @if($livraison->commande->employe)
                <div style="display: grid; gap: 0.75rem;">
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <svg style="width: 16px; height: 16px; color: #FF0000;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <strong>{{ $livraison->commande->employe->name }}</strong>
                    </div>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <svg style="width: 16px; height: 16px; color: #CC0000;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span style="font-size: 0.875rem; color: #666;">{{ $livraison->commande->numero_telephone ?? $livraison->commande->employe->telephone ?? 'N/A' }}</span>
                    </div>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <svg style="width: 16px; height: 16px; color: #10B981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span style="font-size: 0.875rem; color: #666;">{{ $livraison->commande->employe->email }}</span>
                    </div>
                    @if($livraison->commande->employe->entreprise)
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <svg style="width: 16px; height: 16px; color: #000000;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span style="font-size: 0.875rem; color: #666;">{{ $livraison->commande->employe->entreprise->nom }}</span>
                    </div>
                    @endif
                </div>
                @else
                <p style="color: #999;">Information client non disponible</p>
                @endif
            </div>

            <!-- Lieu -->
            @if($livraison->commande->lieu)
            <div class="card" style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #000000; margin-bottom: 1rem;">Lieu de livraison</h3>
                <p style="color: #666; margin-bottom: 1rem;">{{ $livraison->commande->lieu }}</p>
                @if($livraison->commande->lat && $livraison->commande->long)
                <a href="https://www.google.com/maps?q={{ $livraison->commande->lat }},{{ $livraison->commande->long }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1rem; background: #FF0000; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Voir sur la carte
                </a>
                @endif
                @if($livraison->commande->consigne_livreur)
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #E5E5E5;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: #666;">Consigne</label>
                    <p style="color: #666;">{{ $livraison->commande->consigne_livreur }}</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Montants -->
            <div class="card">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #000000; margin-bottom: 1rem;">Montants</h3>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                    <span style="color: #666;">Commande</span>
                    <span style="font-weight: 600;">{{ number_format($livraison->commande->montant_total, 0, ',', ' ') }} FCFA</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding-bottom: 0.75rem; border-bottom: 1px solid #E5E5E5;">
                    <span style="color: #666;">Livraison</span>
                    <span style="font-weight: 600;">{{ number_format($livraison->montant_livraison, 0, ',', ' ') }} FCFA</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 0.75rem;">
                    <span style="font-weight: 600;">Total</span>
                    <span style="font-size: 1.5rem; font-weight: 700; color: #FF0000;">
                        {{ number_format($livraison->commande->montant_total + $livraison->montant_livraison, 0, ',', ' ') }} FCFA
                    </span>
                </div>
            </div>
        </div>
    </div>
@endsection
