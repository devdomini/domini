@extends('commercial.layout')

@section('title', 'Abonnements')
@section('page-title', 'Abonnements')

@section('content')
<div style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.5rem; font-weight: 700; color: #000000;">Abonnements de mes entreprises</h2>
    <p style="color: #666; margin-top: 0.25rem;">Suivi des abonnements du portefeuille</p>
</div>

<div class="card" style="margin-bottom: 1.5rem;">
    <div style="padding: 1rem;">
        <form method="GET">
            <select name="entreprise_id" onchange="this.form.submit()" style="padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px; background: white; min-width: 280px;">
                <option value="">Toutes les entreprises</option>
                @foreach($entreprises as $ent)
                    <option value="{{ $ent->id }}" @selected($entrepriseId == $ent->id)>{{ $ent->nom }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Entreprise</th>
                    <th>Statut</th>
                    <th>Fin</th>
                    <th>Employés</th>
                </tr>
            </thead>
            <tbody>
            @forelse($abonnements as $a)
                <tr>
                    <td>{{ $a->entreprise->nom ?? '—' }}</td>
                    <td><span class="badge badge-info">{{ $a->status }}</span></td>
                    <td>{{ $a->date_fin?->format('d/m/Y') }}</td>
                    <td>{{ $a->nbre_employe }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center;padding:2rem;color:#666;">Aucun abonnement.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding-top: 1rem;">{{ $abonnements->links() }}</div>
</div>
@endsection
