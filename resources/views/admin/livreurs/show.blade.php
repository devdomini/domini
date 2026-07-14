@extends('admin.layout')

@section('title', 'Détails du Livreur')
@section('page-title', 'Détails du Livreur')

@section('content')
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="margin-bottom: 2rem;">
            <a href="{{ route('admin.livreurs.index') }}" style="color: #FF0000; text-decoration: none; font-weight: 600;">
                ← Retour à la liste
            </a>
        </div>

        <!-- Info principale -->
        <div class="card" style="margin-bottom: 2rem;">
            <div style="padding: 2rem;">
                <div style="display: grid; grid-template-columns: auto 1fr auto; gap: 2rem; align-items: center;">
                    <!-- Avatar -->
                    <div style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #FF0000, #CC0000); display: flex; align-items: center; justify-content: center; color: white; font-weight: 900; font-size: 3rem;">
                        {{ strtoupper(substr($livreur->name, 0, 1)) }}
                    </div>

                    <!-- Info -->
                    <div>
                        <h2 style="font-size: 2rem; font-weight: 800; color: #000000; margin-bottom: 0.5rem;">
                            {{ $livreur->name }}
                        </h2>
                        <div style="display: flex; gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap;">
                            @if($livreur->is_active)
                                <span class="badge badge-success">Actif</span>
                            @else
                                <span class="badge badge-danger">Inactif</span>
                            @endif
                            @if($livreur->is_dispo ?? true)
                                <span class="badge" style="background-color: rgba(76,175,80,0.2); color: #2E7D32;">App : disponible</span>
                            @else
                                <span class="badge" style="background-color: rgba(239,68,68,0.2); color: #CC0000;">App : indisponible</span>
                            @endif
                            <span class="badge" style="background-color: #000000; color: white;">ID: #{{ $livreur->id }}</span>
                        </div>
                        @if(!($livreur->is_dispo ?? true) && $livreur->indispo_reason)
                            <div style="background: #FEF2F2; border: 1px solid #FECACA; border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.875rem; color: #991B1B;">
                                <strong>Raison indisponibilité (app) :</strong> {{ $livreur->indispo_reason }}
                                @if($livreur->indispo_at)
                                    <div style="margin-top: 0.35rem; color: #666; font-size: 0.8rem;">{{ $livreur->indispo_at->format('d/m/Y H:i') }}</div>
                                @endif
                            </div>
                        @endif
                        <div style="color: #666;">
                            <div style="margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.375rem;">
                                @include('admin.partials.icon', ['name' => 'mail', 'size' => 14]) {{ $livreur->email }}
                            </div>
                            <div style="margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.375rem;">
                                @include('admin.partials.icon', ['name' => 'phone', 'size' => 14]) {{ $livreur->telephone }}
                            </div>
                            <div style="margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.375rem;">
                                @include('admin.partials.icon', ['name' => 'building', 'size' => 14]) Entrepôt : {{ $livreur->warehouse?->name ?? '—' }}
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.375rem;">
                                @include('admin.partials.icon', ['name' => 'calendar', 'size' => 14]) Inscrit le {{ $livreur->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <a href="{{ route('admin.livreurs.edit', $livreur->id) }}" class="btn btn-primary">
                            Modifier
                        </a>
                        <a href="{{ route('admin.livreurs.change-password', $livreur->id) }}" class="btn btn-secondary">
                            Changer mot de passe
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- Entreprises affectées -->
            <div class="card">
                <div style="padding: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h3 style="font-size: 1.25rem; font-weight: 700; color: #000000; margin: 0;">
                            Entreprises affectées ({{ $livreur->entreprises->count() }})
                        </h3>
                        <button onclick="openAffectModal()" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                            + Gérer
                        </button>
                    </div>
                    
                    @if($livreur->entreprises->count() > 0)
                        <div style="display: grid; gap: 1rem;">
                            @foreach($livreur->entreprises as $entreprise)
                                <div style="background: #F0FDF4; padding: 1rem; border-radius: 8px; border: 2px solid #10B981; display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <div style="font-weight: 700; color: #000000; margin-bottom: 0.25rem;">
                                            {{ $entreprise->nom }}
                                        </div>
                                        <div style="color: #666; font-size: 0.75rem; display: flex; align-items: center; gap: 0.25rem;">
                                            @include('admin.partials.icon', ['name' => 'map-pin', 'size' => 12]) {{ $entreprise->ville }}, {{ $entreprise->pays }}
                                        </div>
                                    </div>
                                    <form action="{{ route('admin.livreurs.remove-entreprise', $livreur->id) }}" method="POST" onsubmit="return confirm('Retirer {{ $entreprise->nom }} ?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="entreprise_id" value="{{ $entreprise->id }}">
                                        <button type="submit" style="background: #EF4444; color: white; border: none; padding: 0.5rem 0.75rem; border-radius: 6px; cursor: pointer; font-size: 0.875rem; font-weight: 600;">
                                            Retirer
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>

                        <form action="{{ route('admin.livreurs.remove-all-entreprises', $livreur->id) }}" method="POST" onsubmit="return confirm('Retirer TOUTES les entreprises de ce livreur ?')" style="margin-top: 1rem;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-secondary" style="width: 100%;">
                                Retirer toutes les entreprises
                            </button>
                        </form>
                    @else
                        <div style="background: #FEF3C7; padding: 1.5rem; border-radius: 12px; text-align: center;">
                            <svg style="width: 48px; height: 48px; margin: 0 auto 1rem; color: #92400E;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <p style="font-weight: 600; color: #92400E; margin-bottom: 1rem;">Aucune entreprise affectée</p>
                            <p style="font-size: 0.875rem; color: #666;">Ce livreur n'est assigné à aucune entreprise pour le moment</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistiques -->
            <div class="card">
                <div style="padding: 2rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: #000000; margin-bottom: 1.5rem;">
                        Statistiques de livraison
                    </h3>
                    
                    <div style="display: grid; gap: 1rem;">
                        <div style="background: linear-gradient(135deg, #FF0000, #CC0000); padding: 1.5rem; border-radius: 12px; color: white;">
                            <div style="font-size: 0.875rem; opacity: 0.9;">Total livraisons</div>
                            <div style="font-size: 2.5rem; font-weight: 900; margin: 0.5rem 0;">{{ $stats['livraisons_total'] }}</div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div style="background: #F0FDF4; padding: 1rem; border-radius: 8px;">
                                <div style="font-size: 0.75rem; color: #166534;">Ce mois</div>
                                <div style="font-size: 1.5rem; font-weight: 800; color: #10B981;">{{ $stats['livraisons_mois'] }}</div>
                            </div>
                            <div style="background: #FEF3C7; padding: 1rem; border-radius: 8px;">
                                <div style="font-size: 0.75rem; color: #92400E;">Aujourd'hui</div>
                                <div style="font-size: 1.5rem; font-weight: 800; color: #CC0000;">{{ $stats['livraisons_jour'] }}</div>
                            </div>
                        </div>
                        
                        <div style="background: #DBEAFE; padding: 1rem; border-radius: 8px;">
                            <div style="font-size: 0.75rem; color: #1E40AF;">En cours</div>
                            <div style="font-size: 1.5rem; font-weight: 800; color: #3B82F6;">{{ $stats['en_cours'] }}</div>
                        </div>
                    </div>

                    <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #E5E5E5; text-align: center; color: #666; font-size: 0.875rem;">
                        Les statistiques de livraison seront disponibles après implémentation du système de commandes
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Affecter Entreprises (Multiple) -->
    <div id="affectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 16px; padding: 2rem; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto;">
            <h3 style="font-size: 1.5rem; font-weight: 700; color: #000000; margin-bottom: 0.5rem;">
                Gérer les entreprises
            </h3>
            <p style="color: #666; margin-bottom: 1.5rem; font-size: 0.875rem;">
                Livreur: {{ $livreur->name }}
            </p>
            
            <form action="{{ route('admin.livreurs.affect-entreprises', $livreur->id) }}" method="POST">
                @csrf
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.75rem; color: #000000;">
                        Sélectionnez les entreprises <span style="color: #FF0000;">*</span>
                    </label>
                    <div style="max-height: 300px; overflow-y: auto; border: 2px solid #E5E5E5; border-radius: 8px; padding: 1rem;">
                        @foreach($entreprisesDisponibles as $entreprise)
                            <label style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; cursor: pointer; border-radius: 6px; transition: background 0.2s;" onmouseover="this.style.background='#F5F5F5'" onmouseout="this.style.background='transparent'">
                                <input 
                                    type="checkbox" 
                                    name="entreprises[]" 
                                    value="{{ $entreprise->id }}" 
                                    {{ $livreur->entreprises->contains($entreprise->id) ? 'checked' : '' }}
                                    style="width: 18px; height: 18px; cursor: pointer;"
                                >
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; color: #000000;">{{ $entreprise->nom }}</div>
                                    <div style="font-size: 0.75rem; color: #666;">{{ $entreprise->ville }}, {{ $entreprise->pays }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <small style="color: #666; font-size: 0.75rem; margin-top: 0.5rem; display: flex; align-items: center; gap: 0.25rem;">
                        @include('admin.partials.icon', ['name' => 'check', 'size' => 12]) Cochez une ou plusieurs entreprises
                    </small>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        Enregistrer
                    </button>
                    <button type="button" onclick="closeAffectModal()" class="btn btn-secondary">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAffectModal() {
            document.getElementById('affectModal').style.display = 'flex';
        }

        function closeAffectModal() {
            document.getElementById('affectModal').style.display = 'none';
        }

        document.getElementById('affectModal').addEventListener('click', function(e) {
            if (e.target === this) closeAffectModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeAffectModal();
        });
    </script>
@endsection
