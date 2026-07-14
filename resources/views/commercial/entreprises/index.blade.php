@extends('commercial.layout')

@section('title', 'Entreprises')
@section('page-title', 'Entreprises')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 700; color: #000000;">Toutes les entreprises</h2>
        <p style="color: #666; margin-top: 0.25rem;">Liste complète des entreprises Domini</p>
    </div>
    <a href="{{ route('commercial.entreprises.create') }}" class="btn btn-primary">+ Ajouter une entreprise</a>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-label">Total</div>
        <div class="stat-value">{{ $entreprises->total() }}</div>
    </div>
    <div class="stat-card yellow">
        <div class="stat-label">Actives</div>
        <div class="stat-value">{{ $entreprises->getCollection()->where('statut', true)->count() }}</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem;">
    @forelse($entreprises as $entreprise)
    <div class="card" style="overflow: hidden; padding: 0;">
        <div style="background: linear-gradient(135deg, #FF0000, #CC0000); padding: 2rem; text-align: center; position: relative;">
            @if($entreprise->logo)
                <img src="{{ asset('storage/'.$entreprise->logo) }}" alt="{{ $entreprise->nom }}" style="width: 80px; height: 80px; border-radius: 50%; border: 4px solid white; object-fit: cover;">
            @else
                <div style="width: 80px; height: 80px; margin: 0 auto; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 900; color: #FF0000;">
                    {{ strtoupper(substr($entreprise->nom, 0, 1)) }}
                </div>
            @endif
            <div style="position: absolute; top: 1rem; right: 1rem;">
                @if($entreprise->statut)
                    <span class="badge badge-success">Active</span>
                @else
                    <span class="badge badge-danger">Inactive</span>
                @endif
            </div>
        </div>
        <div style="padding: 1.5rem;">
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #000000; margin-bottom: 0.75rem;">{{ $entreprise->nom }}</h3>
            <p style="color: #666; font-size: 0.875rem; margin-bottom: 0.5rem;">{{ $entreprise->adresse }}</p>
            <p style="color: #666; font-size: 0.875rem; margin-bottom: 1rem;">{{ $entreprise->ville }}@if($entreprise->numero) · {{ $entreprise->numero }}@endif</p>
            @if($entreprise->commercial)
                <p style="color: #666; font-size: 0.8rem; margin-bottom: 1rem;">Commercial : {{ $entreprise->commercial->name }}</p>
            @endif
            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid #E5E5E5;">
                <span style="color: #666; font-size: 0.875rem;">{{ $entreprise->employes()->count() }} employé(s)</span>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('commercial.entreprises.show', $entreprise->id) }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Voir</a>
                    <a href="{{ route('commercial.entreprises.edit', $entreprise->id) }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Modifier</a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
        <p style="color: #666; margin-bottom: 1rem;">Aucune entreprise enregistrée.</p>
        <a href="{{ route('commercial.entreprises.create') }}" class="btn btn-primary">Créer la première entreprise</a>
    </div>
    @endforelse
</div>

<div style="margin-top: 2rem;">{{ $entreprises->links() }}</div>
@endsection
