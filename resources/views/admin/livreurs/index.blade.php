@extends('admin.layout')

@section('title', 'Gestion des Livreurs')
@section('page-title', 'Gestion des Livreurs')

@section('content')
    <!-- Messages -->
    @if(session('success'))
        <div style="background-color: #10B981; color: white; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
            <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="card" style="background: linear-gradient(135deg, #FF0000, #CC0000); color: white;">
            <div style="padding: 1.5rem;">
                <div style="font-size: 0.875rem; opacity: 0.9;">Total Livreurs</div>
                <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ $totalLivreurs }}</div>
            </div>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #10B981, #059669); color: white;">
            <div style="padding: 1.5rem;">
                <div style="font-size: 0.875rem; opacity: 0.9;">Actifs</div>
                <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ $livreursActifs }}</div>
            </div>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #EF4444, #DC2626); color: white;">
            <div style="padding: 1.5rem;">
                <div style="font-size: 0.875rem; opacity: 0.9;">Inactifs</div>
                <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ $livreursInactifs }}</div>
            </div>
        </div>
    </div>

    <!-- Header & Filters -->
    <div class="card" style="margin-bottom: 2rem;">
        <div style="padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="font-size: 1.25rem; font-weight: 700; color: #000000; margin: 0;">
                    Liste des Livreurs ({{ $livreurs->total() }})
                </h2>
                <a href="{{ route('admin.livreurs.create') }}" class="btn btn-primary">
                    + Ajouter un livreur
                </a>
            </div>

            <!-- Filtres -->
            <form method="GET" action="{{ route('admin.livreurs.index') }}" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr auto; gap: 1rem;">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Rechercher par nom, email, téléphone..." 
                    value="{{ request('search') }}"
                    style="padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;"
                >
                <select name="status" style="padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actifs</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactifs</option>
                </select>
                <select name="entreprise" style="padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                    <option value="">Toutes entreprises</option>
                    @foreach($entreprises as $entreprise)
                        <option value="{{ $entreprise->id }}" {{ request('entreprise') == $entreprise->id ? 'selected' : '' }}>
                            {{ $entreprise->nom }}
                        </option>
                    @endforeach
                </select>
                <select name="type_livreur" style="padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                    <option value="">Tous types</option>
                    <option value="classique" {{ request('type_livreur') == 'classique' ? 'selected' : '' }}>Classique</option>
                    <option value="entreprise" {{ request('type_livreur') == 'entreprise' ? 'selected' : '' }}>Entreprise (lot)</option>
                </select>
                <select name="dispo" style="padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;" title="Sans course assignée/en cours">
                    <option value="">Courses (tous)</option>
                    <option value="1" {{ request('dispo') === '1' ? 'selected' : '' }}>Sans course active</option>
                    <option value="0" {{ request('dispo') === '0' ? 'selected' : '' }}>Avec course active</option>
                </select>
                <select name="dispo_app" style="padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;" title="Statut déclaré dans l’app mobile">
                    <option value="">App (tous)</option>
                    <option value="1" {{ request('dispo_app') === '1' ? 'selected' : '' }}>App disponible</option>
                    <option value="0" {{ request('dispo_app') === '0' ? 'selected' : '' }}>App indisponible</option>
                </select>
                <button type="submit" class="btn btn-secondary">Filtrer</button>
            </form>
        </div>
    </div>

    <!-- Liste des Livreurs -->
    <div class="card">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background-color: #FDFBF8; border-bottom: 2px solid #E5E5E5;">
                    <tr>
                        <th style="padding: 1rem; text-align: left; font-weight: 700; color: #000000;">Livreur</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 700; color: #000000;">Contact</th>
                        <th style="padding: 1rem; text-align: center; font-weight: 700; color: #000000;">Type</th>
                        <th style="padding: 1rem; text-align: center; font-weight: 700; color: #000000;">Dispo app</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 700; color: #000000;">Entrepôt</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 700; color: #000000;">Entreprise(s)</th>
                        <th style="padding: 1rem; text-align: center; font-weight: 700; color: #000000;">Statut</th>
                        <th style="padding: 1rem; text-align: center; font-weight: 700; color: #000000;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($livreurs as $livreur)
                    <tr style="border-bottom: 1px solid #E5E5E5;">
                        <td style="padding: 1rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, #FF0000, #CC0000); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.25rem;">
                                    {{ strtoupper(substr($livreur->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #000000;">{{ $livreur->name }}</div>
                                    <div style="font-size: 0.875rem; color: #666;">ID: #{{ $livreur->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 1rem;">
                            <div style="font-size: 0.875rem;">
                                <div style="margin-bottom: 0.25rem;">{{ $livreur->email }}</div>
                                <div style="color: #666;">{{ $livreur->telephone }}</div>
                            </div>
                        </td>
                        <td style="padding: 1rem; text-align: center; font-size: 0.875rem;">
                            @php
                                $t = $livreur->type_livreur;
                            @endphp
                            @if($t === 'entreprise')
                                <span class="badge" style="background-color: rgba(25,118,210,0.12); color: #1976D2; font-weight: 700;">Entreprise</span>
                            @elseif($t === 'classique')
                                <span class="badge" style="background-color: rgba(76,175,80,0.12); color: #4CAF50; font-weight: 700;">Classique</span>
                            @else
                                <span style="color: #999;">—</span>
                            @endif
                        </td>
                        <td style="padding: 1rem; text-align: center; font-size: 0.8rem;">
                            @php
                                $appDispo = $livreur->is_dispo ?? true;
                            @endphp
                            @if($appDispo)
                                <span class="badge" style="background-color: rgba(76,175,80,0.15); color: #2E7D32; font-weight: 700;">Disponible</span>
                            @else
                                <span class="badge" style="background-color: rgba(239,68,68,0.15); color: #CC0000; font-weight: 700; cursor: help;"
                                      title="{{ $livreur->indispo_reason ? e($livreur->indispo_reason) : 'Indisponible' }}">
                                    Indisponible
                                </span>
                                @if($livreur->indispo_reason)
                                    <div style="font-size: 0.7rem; color: #666; margin-top: 0.25rem; max-width: 140px; margin-left: auto; margin-right: auto; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ e($livreur->indispo_reason) }}">
                                        {{ \Illuminate\Support\Str::limit($livreur->indispo_reason, 40) }}
                                    </div>
                                @endif
                            @endif
                        </td>
                        <td style="padding: 1rem; font-size: 0.875rem;">
                            @if($livreur->warehouse)
                                <span style="color: #000000;">{{ $livreur->warehouse->name }}</span>
                            @else
                                <span style="color: #999;">—</span>
                            @endif
                        </td>
                        <td style="padding: 1rem;">
                            @if($livreur->entreprises->count() > 0)
                                <div style="display: flex; flex-wrap: wrap; gap: 0.25rem;">
                                    @foreach($livreur->entreprises as $entreprise)
                                        <span class="badge badge-success" style="font-size: 0.7rem;">{{ $entreprise->nom }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="badge" style="background-color: #E5E5E5; color: #666;">Non affecté</span>
                            @endif
                        </td>
                        <td style="padding: 1rem; text-align: center;">
                            <form action="{{ route('admin.livreurs.toggle-status', $livreur->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <label class="switch">
                                    <input type="checkbox" {{ $livreur->is_active ? 'checked' : '' }} onchange="this.form.submit()">
                                    <span class="slider round"></span>
                                </label>
                            </form>
                        </td>
                        <td style="padding: 1rem;">
                            <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                <!-- Voir -->
                                <a href="{{ route('admin.livreurs.show', $livreur->id) }}" class="btn-icon" style="background-color: #000000;" title="Voir détails">
                                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <!-- Modifier -->
                                <a href="{{ route('admin.livreurs.edit', $livreur->id) }}" class="btn-icon btn-icon-warning" title="Modifier">
                                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <!-- Affecter entreprises -->
                                <button onclick='openAffectModal({{ $livreur->id }}, "{{ $livreur->name }}", @json($livreur->entreprises->pluck("id")))' class="btn-icon" style="background-color: #10B981;" title="Gérer entreprises">
                                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </button>
                                <!-- Mot de passe -->
                                <a href="{{ route('admin.livreurs.change-password', $livreur->id) }}" class="btn-icon" style="background-color: #8B5CF6;" title="Changer mot de passe">
                                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="padding: 3rem; text-align: center; color: #666;">
                            <svg style="width: 64px; height: 64px; margin: 0 auto 1rem; opacity: 0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <p style="font-weight: 600; margin-bottom: 0.5rem;">Aucun livreur trouvé</p>
                            <p>Ajoutez un livreur pour commencer</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($livreurs->hasPages())
        <div style="padding: 1.5rem; border-top: 1px solid #E5E5E5;">
            {{ $livreurs->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Affecter Entreprises (Multiple) -->
    <div id="affectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 16px; padding: 2rem; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto;">
            <h3 style="font-size: 1.5rem; font-weight: 700; color: #000000; margin-bottom: 0.5rem;">
                Gérer les entreprises
            </h3>
            <p id="affectLivreurName" style="color: #666; margin-bottom: 1.5rem; font-size: 0.875rem;"></p>
            
            <form id="affectForm" method="POST">
                @csrf
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.75rem; color: #000000;">
                        Sélectionnez les entreprises <span style="color: #FF0000;">*</span>
                    </label>
                    <div style="max-height: 300px; overflow-y: auto; border: 2px solid #E5E5E5; border-radius: 8px; padding: 1rem;">
                        @foreach($entreprises as $entreprise)
                            <label style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; cursor: pointer; border-radius: 6px; transition: background 0.2s;" onmouseover="this.style.background='#F5F5F5'" onmouseout="this.style.background='transparent'">
                                <input 
                                    type="checkbox" 
                                    name="entreprises[]" 
                                    value="{{ $entreprise->id }}" 
                                    class="entreprise-checkbox"
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

    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: #10B981;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .slider.round {
            border-radius: 24px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        .btn-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: none;
            color: white;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-icon-warning {
            background-color: #CC0000;
        }

        .btn-icon:hover {
            transform: scale(1.1);
        }
    </style>

    <script>
        function openAffectModal(livreurId, livreurName, currentEntreprises) {
            document.getElementById('affectModal').style.display = 'flex';
            document.getElementById('affectLivreurName').textContent = 'Livreur: ' + livreurName + ' - Sélectionnez les entreprises à affecter';
            document.getElementById('affectForm').action = `/admin/livreurs/${livreurId}/affect-entreprises`;
            
            // Décocher toutes les checkboxes
            document.querySelectorAll('.entreprise-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Cocher les entreprises actuelles
            if (currentEntreprises && currentEntreprises.length > 0) {
                currentEntreprises.forEach(entrepriseId => {
                    const checkbox = document.querySelector(`.entreprise-checkbox[value="${entrepriseId}"]`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
            }
        }

        function closeAffectModal() {
            document.getElementById('affectModal').style.display = 'none';
        }

        // Close modal on outside click
        document.getElementById('affectModal').addEventListener('click', function(e) {
            if (e.target === this) closeAffectModal();
        });

        // Close modal on Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeAffectModal();
        });
    </script>

    <!-- Pagination -->
    {{ $livreurs->links('vendor.pagination.domini') }}
@endsection
