@extends('admin.layout')

@section('title', 'Trajets de livraison')
@section('page-title', 'Trajets de livraison')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #000000;">Ordre de livraison par entrepôt</h2>
            <p style="color: #666; max-width: 640px; margin-top: 0.5rem;">
                Pour chaque entrepôt, définissez l’ordre dans lequel les entreprises sont livrées. Seules les entreprises
                <strong>actives</strong> dont la <strong>commune</strong> est rattachée à l’entrepôt peuvent être ajoutées au trajet.
            </p>
        </div>
        <a href="{{ route('admin.warehouses.index') }}" style="padding: 0.65rem 1.25rem; border: 1px solid #D1D5DB; border-radius: 8px; text-decoration: none; color: #374151; font-weight: 600;">
            ← Entrepôts
        </a>
    </div>

    @if(session('success'))
        <div style="background: #D4EDDA; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">{{ session('success') }}</div>
    @endif

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #F8F9FA; text-align: left;">
                    <th style="padding: 1rem; color: #666; font-weight: 600;">Entrepôt</th>
                    <th style="padding: 1rem; color: #666; font-weight: 600;">Ville</th>
                    <th style="padding: 1rem; color: #666; font-weight: 600;">Étapes dans le trajet</th>
                    <th style="padding: 1rem; color: #666; font-weight: 600;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($warehouses as $w)
                    <tr style="border-bottom: 1px solid #EEE;">
                        <td style="padding: 1rem; font-weight: 600;">{{ $w->name }}</td>
                        <td style="padding: 1rem;">{{ $w->city }}</td>
                        <td style="padding: 1rem;">
                            <span style="background: #EFF6FF; color: #1D4ED8; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                {{ $w->trajet_items_count }} entreprise(s)
                            </span>
                        </td>
                        <td style="padding: 1rem;">
                            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                <a href="{{ route('admin.trajets.edit', $w) }}" style="background: #FF0000; color: white; padding: 0.5rem 1rem; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
                                    Définir le trajet
                                </a>
                                <a href="{{ route('admin.trajets.map', $w) }}" style="background: #3B82F6; color: white; padding: 0.5rem 1rem; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
                                    Carte
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 2rem; text-align: center; color: #666;">Aucun entrepôt. Créez d’abord un entrepôt.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
