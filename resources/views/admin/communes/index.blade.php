@extends('admin.layout')

@section('title', 'Communes')
@section('page-title', 'Communes')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #000000;">Communes</h2>
            <p style="color: #666;">Rattachées à un entrepôt (coordonnées GPS optionnelles) — utilisées pour les entreprises et les trajets.</p>
        </div>
        <a href="{{ route('admin.communes.create') }}" style="background: #FF0000; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600;">
            + Ajouter une commune
        </a>
    </div>

    @if(session('success'))
        <div style="background: #D4EDDA; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div style="background: #F8D7DA; color: #721C24; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">{{ session('error') }}</div>
    @endif

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #F8F9FA; text-align: left;">
                    <th style="padding: 1rem;">Commune</th>
                    <th style="padding: 1rem;">Entrepôt</th>
                    <th style="padding: 1rem;">Coordonnées</th>
                    <th style="padding: 1rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($communes as $commune)
                    <tr style="border-bottom: 1px solid #EEE;">
                        <td style="padding: 1rem; font-weight: 600;">{{ $commune->nom }}</td>
                        <td style="padding: 1rem;">{{ $commune->warehouse?->name ?? '—' }}</td>
                        <td style="padding: 1rem; font-family: monospace; font-size: 0.85rem;">{{ $commune->latitude !== null && $commune->longitude !== null ? $commune->latitude.', '.$commune->longitude : '—' }}</td>
                        <td style="padding: 1rem;">
                            <a href="{{ route('admin.communes.edit', $commune) }}" style="color: #3B82F6; margin-right: 0.5rem;">Modifier</a>
                            <form action="{{ route('admin.communes.destroy', $commune) }}" method="POST" style="display: inline;" onsubmit="return confirm('Supprimer cette commune ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="color: #EF4444; background: none; border: none; cursor: pointer;">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 2rem; text-align: center; color: #666;">Aucune commune. Créez-en une et rattachez-la à un entrepôt.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($communes->hasPages())
        <div style="margin-top: 1.5rem;">{{ $communes->links() }}</div>
    @endif
@endsection
