@extends('admin.layout')

@section('title', 'Entreprises')
@section('page-title', 'Gestion des Entreprises')

@section('content')
    <!-- Actions Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #000000;">Toutes les entreprises</h2>
            <p style="color: #666; margin-top: 0.25rem;">Gérez les entreprises partenaires Domini</p>
        </div>
        <a href="{{ route('admin.entreprises.create') }}" class="btn btn-primary">
            + Ajouter une entreprise
        </a>
    </div>

    <!-- Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div style="background: linear-gradient(135deg, #FF0000, #CC0000); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Total Entreprises</div>
            <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ $entreprises->count() }}</div>
        </div>
        <div style="background: linear-gradient(135deg, #CC0000, #990000); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Entreprises Actives</div>
            <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ $entreprises->where('statut', true)->count() }}</div>
        </div>
        <div style="background: linear-gradient(135deg, #1A1A1A, #000000); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Total Employés</div>
            <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ $totalEmployes ?? 0 }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <div style="padding: 1rem; display: flex; gap: 1rem; flex-wrap: wrap;">
            <select style="padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px; background: white;">
                <option value="">Tous les statuts</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
            
            <select style="padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px; background: white;">
                <option value="">Toutes les villes</option>
                <option value="abidjan">Abidjan</option>
                <option value="bouake">Bouaké</option>
                <option value="yamoussoukro">Yamoussoukro</option>
            </select>
            
            <input type="text" placeholder="Rechercher par nom..." style="padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px; flex: 1; min-width: 200px;">
        </div>
    </div>

    <!-- Entreprises Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem;">
        @forelse($entreprises ?? [] as $entreprise)
        <div class="card" style="overflow: hidden;">
            <!-- Header with Logo -->
            <div style="background: linear-gradient(135deg, #FF0000, #CC0000); padding: 2rem; text-align: center; position: relative;">
                @if($entreprise->logo)
                    <img src="{{ asset('storage/' . $entreprise->logo) }}" alt="{{ $entreprise->nom }}" style="width: 80px; height: 80px; border-radius: 50%; border: 4px solid white; object-fit: cover;">
                @else
                    <div style="width: 80px; height: 80px; margin: 0 auto; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 900; color: #FF0000;">
                        {{ strtoupper(substr($entreprise->nom, 0, 1)) }}
                    </div>
                @endif
                
                <!-- Status Badge -->
                <div style="position: absolute; top: 1rem; right: 1rem;">
                    @if($entreprise->statut)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-danger">Inactive</span>
                    @endif
                </div>
            </div>

            <!-- Content -->
            <div style="padding: 1.5rem;">
                <h3 style="font-size: 1.25rem; font-weight: 700; color: #000000; margin-bottom: 1rem;">
                    {{ $entreprise->nom }}
                </h3>

                <div style="display: grid; gap: 0.75rem; margin-bottom: 1.5rem;">
                    <!-- Adresse -->
                    <div style="display: flex; gap: 0.5rem; align-items: flex-start;">
                        <svg style="width: 16px; height: 16px; color: #FF0000; flex-shrink: 0; margin-top: 2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span style="color: #666; font-size: 0.875rem; line-height: 1.4;">{{ $entreprise->adresse }}</span>
                    </div>

                    <!-- Ville -->
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <svg style="width: 16px; height: 16px; color: #CC0000;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span style="color: #666; font-size: 0.875rem;">{{ $entreprise->ville }}, {{ $entreprise->pays }}</span>
                    </div>

                    @if($entreprise->commune)
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <span style="color: #FF0000; font-size: 0.75rem; font-weight: 700;">COMMUNE</span>
                        <span style="color: #666; font-size: 0.875rem;">{{ $entreprise->commune->nom }} @if($entreprise->commune->warehouse) · {{ $entreprise->commune->warehouse->name }} @endif</span>
                    </div>
                    @endif

                    <!-- Téléphone -->
                    @if($entreprise->numero)
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <svg style="width: 16px; height: 16px; color: #000000;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span style="color: #666; font-size: 0.875rem;">{{ $entreprise->numero }}</span>
                    </div>
                    @endif

                    <!-- Employés -->
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <svg style="width: 16px; height: 16px; color: #FF0000;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span style="color: #666; font-size: 0.875rem;">{{ $entreprise->employes->count() }} employés</span>
                    </div>
                </div>

                <!-- Actions -->
                <div style="display: flex; gap: 0.5rem; padding-top: 1rem; border-top: 1px solid #E5E5E5;">
                    <a href="{{ route('admin.entreprises.edit', $entreprise->id) }}" style="flex: 1; padding: 0.625rem; background-color: #CC0000; color: white; border-radius: 6px; text-decoration: none; font-size: 0.875rem; font-weight: 600; text-align: center; display: inline-flex; align-items: center; justify-content: center; gap: 0.375rem;">
                        @include('admin.partials.icon', ['name' => 'pencil', 'size' => 16]) Modifier
                    </a>
                    <form action="{{ route('admin.entreprises.destroy', $entreprise->id) }}" method="POST" style="flex: 1;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette entreprise ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="width: 100%; padding: 0.625rem; background-color: #CC0000; color: white; border: none; border-radius: 6px; font-size: 0.875rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 0.375rem;">
                            @include('admin.partials.icon', ['name' => 'trash', 'size' => 16]) Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 4rem; color: #666;">
            <svg style="width: 64px; height: 64px; margin: 0 auto 1rem; opacity: 0.3;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <p style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem;">Aucune entreprise</p>
            <p style="font-size: 0.875rem;">Commencez par ajouter votre première entreprise partenaire</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    {{ $entreprises->links('vendor.pagination.domini') }}
@endsection
