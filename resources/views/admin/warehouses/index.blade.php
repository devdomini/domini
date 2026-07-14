@extends('admin.layout')

@section('title', 'Entrepôts')
@section('page-title', 'Gestion des Entrepôts')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #000000;">Liste des Entrepôts</h2>
            <p style="color: #666;">Gérez les points de départ des livraisons</p>
        </div>
        <a href="{{ route('admin.warehouses.create') }}" style="background: #FF0000; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease;">
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajouter un entrepôt
        </a>
    </div>

    @if(session('success'))
        <div style="background: #D4EDDA; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #C3E6CB;">
            {{ session('success') }}
        </div>
    @endif

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #F8F9FA; text-align: left;">
                    <th style="padding: 1rem; color: #666; font-weight: 600; font-size: 0.9rem;">Nom</th>
                    <th style="padding: 1rem; color: #666; font-weight: 600; font-size: 0.9rem;">Adresse</th>
                    <th style="padding: 1rem; color: #666; font-weight: 600; font-size: 0.9rem;">Ville</th>
                    <th style="padding: 1rem; color: #666; font-weight: 600; font-size: 0.9rem;">Coordonnées</th>
                    <th style="padding: 1rem; color: #666; font-weight: 600; font-size: 0.9rem;">Statut</th>
                    <th style="padding: 1rem; color: #666; font-weight: 600; font-size: 0.9rem;">Trajet</th>
                    <th style="padding: 1rem; color: #666; font-weight: 600; font-size: 0.9rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($warehouses as $warehouse)
                    <tr style="border-bottom: 1px solid #EEE;">
                        <td style="padding: 1rem; font-weight: 600;">{{ $warehouse->name }}</td>
                        <td style="padding: 1rem;">{{ $warehouse->address }}</td>
                        <td style="padding: 1rem;">{{ $warehouse->city }}</td>
                        <td style="padding: 1rem; font-family: monospace;">{{ $warehouse->latitude }}, {{ $warehouse->longitude }}</td>
                        <td style="padding: 1rem;">
                            @if($warehouse->is_active)
                                <span style="background: #D4EDDA; color: #155724; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">Actif</span>
                            @else
                                <span style="background: #F8D7DA; color: #721C24; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">Inactif</span>
                            @endif
                        </td>
                        <td style="padding: 1rem;">
                            <a href="{{ route('admin.trajets.edit', $warehouse) }}" style="font-size: 0.85rem; font-weight: 600; color: #FF0000; text-decoration: none;">Ordre livraison</a>
                            <div style="font-size: 0.75rem; color: #9CA3AF; margin-top: 0.25rem;">{{ $warehouse->trajet_items_count }} étape(s)</div>
                        </td>
                        <td style="padding: 1rem;">
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('admin.warehouses.edit', $warehouse->id) }}" style="color: #3B82F6; text-decoration: none; padding: 0.5rem; border-radius: 4px; background: #EFF6FF;" title="Modifier">
                                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.warehouses.destroy', $warehouse->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="color: #EF4444; background: #FEF2F2; border: none; padding: 0.5rem; border-radius: 4px; cursor: pointer;" title="Supprimer">
                                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-7 0h8"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 2rem; text-align: center; color: #666;">Aucun entrepôt configuré.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
