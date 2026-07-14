@extends('commercial.layout')

@section('title', $entreprise->nom)
@section('page-title', $entreprise->nom)

@section('content')
<a href="{{ route('commercial.entreprises.index') }}" class="commercial-back-link">← Entreprises</a>

<div class="commercial-box-header">
    <a href="{{ route('commercial.entreprises.edit', $entreprise->id) }}" class="btn btn-secondary">Modifier</a>
    <a href="{{ route('commercial.boxes.create', $entreprise->id) }}" class="btn btn-primary">+ Créer une box</a>
</div>

<div class="card" style="margin-bottom: 1.5rem;">
    <div style="display: flex; gap: 1.5rem; align-items: start; flex-wrap: wrap;">
        @if($entreprise->logo)
            <img src="{{ asset('storage/'.$entreprise->logo) }}" alt="{{ $entreprise->nom }}" style="width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 3px solid #FF0000;">
        @endif
        <div>
            <p style="font-weight: 700; font-size: 1.125rem; margin-bottom: 0.5rem;">{{ $entreprise->adresse }}</p>
            <p style="color: #666;">{{ $entreprise->ville }}@if($entreprise->numero) · {{ $entreprise->numero }}@endif</p>
            @if($entreprise->lat && $entreprise->long)
                <p style="color: #666; font-size: 0.875rem; margin-top: 0.35rem;">GPS : {{ $entreprise->lat }}, {{ $entreprise->long }}</p>
            @endif
            @if($entreprise->statut)
                <span class="badge badge-success" style="margin-top: 0.5rem;">Active</span>
            @else
                <span class="badge badge-danger" style="margin-top: 0.5rem;">Inactive</span>
            @endif
        </div>
    </div>
</div>

<p class="commercial-section-title">Boxes</p>
@forelse($entreprise->boxes as $box)
    <div class="card" style="margin-bottom: 0.75rem; padding: 1rem;">
        <a href="{{ route('commercial.boxes.show', $box->id) }}" style="color: #FF0000; font-weight: 700; text-decoration: none;"><strong>{{ $box->nom }}</strong> ({{ $box->ref }})</a>
        <span class="commercial-muted"> — {{ $box->casiers->count() }} casiers</span>
    </div>
@empty
    <div class="card"><p class="commercial-muted" style="margin: 0;">Aucune box.</p></div>
@endforelse

<p class="commercial-section-title" style="margin-top: 2rem;">Employés</p>

<div class="card" style="margin-bottom: 1rem;">
    <div class="card-header" style="margin-bottom: 1rem;">
        <h3 class="card-title" style="font-size: 1rem;">Ajouter un employé</h3>
    </div>
    <form method="POST" action="{{ route('commercial.employes.store', $entreprise->id) }}">
        @csrf
        <div style="display: grid; gap: 1rem; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Nom *</label>
                <input type="text" name="name" value="{{ old('name') }}" required style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Téléphone</label>
                <input type="tel" name="telephone" value="{{ old('telephone') }}" style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Mot de passe *</label>
                <input type="password" name="password" required minlength="8" style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
            </div>
        </div>
        <label style="display: flex; align-items: center; gap: 0.5rem; margin-top: 1rem;">
            <input type="checkbox" name="is_active" value="1" checked style="width: 18px; height: 18px;">
            <span style="font-weight: 600;">Compte actif</span>
        </label>
        <div class="commercial-actions">
            <button type="submit" class="btn btn-primary">Ajouter l'employé</button>
        </div>
    </form>
</div>

<div class="card">
    @forelse($entreprise->employes as $emp)
        <details style="margin-bottom: 1rem; border-bottom: 1px solid #E5E5E5; padding-bottom: 1rem;">
            <summary style="cursor: pointer; font-weight: 600; color: #000;">
                {{ $emp->name }} — {{ $emp->email }}
                @if($emp->num_box)<span class="commercial-muted">(casier {{ $emp->num_box }})</span>@endif
                @if(!$emp->is_active)<span class="badge badge-warning">Inactif</span>@endif
            </summary>
            <form method="POST" action="{{ route('commercial.employes.update', $emp->id) }}" style="margin-top: 1rem;">
                @csrf
                @method('PUT')
                <div style="display: grid; gap: 1rem; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Nom *</label>
                        <input type="text" name="name" value="{{ old('name', $emp->name) }}" required style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Email *</label>
                        <input type="email" name="email" value="{{ old('email', $emp->email) }}" required style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Téléphone</label>
                        <input type="tel" name="telephone" value="{{ old('telephone', $emp->telephone) }}" style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Nouveau mot de passe</label>
                        <input type="password" name="password" minlength="8" style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                    </div>
                </div>
                <label style="display: flex; align-items: center; gap: 0.5rem; margin-top: 1rem;">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $emp->is_active)) style="width: 18px; height: 18px;">
                    <span style="font-weight: 600;">Compte actif</span>
                </label>
                <div class="commercial-actions">
                    <button type="submit" class="btn btn-secondary">Enregistrer</button>
                </div>
            </form>
        </details>
    @empty
        <p class="commercial-muted" style="margin: 0;">Aucun employé.</p>
    @endforelse
</div>
@endsection
