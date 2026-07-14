@extends('commercial.layout')

@section('title', 'Nouvelle box')
@section('page-title', 'Créer une box — '.$entreprise->nom)

@section('content')
<div style="max-width: 520px;">
    <a href="{{ route('commercial.entreprises.show', $entreprise->id) }}" class="commercial-back-link">← Entreprise</a>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Nouvelle box</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('commercial.boxes.store') }}">
                @csrf
                <input type="hidden" name="id_entreprise" value="{{ $entreprise->id }}">
                <div style="display: grid; gap: 1.5rem;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Nom de la box <span style="color: #FF0000;">*</span></label>
                        <input type="text" name="nom" required style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Nombre de casiers <span style="color: #FF0000;">*</span></label>
                        <input type="number" name="capacite" min="1" max="200" value="20" required style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Adresse (optionnel)</label>
                        <input type="text" name="adresse" style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                    </div>
                    <button type="submit" class="btn btn-primary">Créer box + casiers</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
