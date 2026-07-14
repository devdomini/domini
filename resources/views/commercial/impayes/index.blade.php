@extends('commercial.layout')

@section('title', 'Impayés')
@section('page-title', 'Commandes impayées')

@section('content')
<div style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.5rem; font-weight: 700; color: #000000;">Commandes impayées</h2>
    <p style="color: #666; margin-top: 0.25rem;">Suivi des impayés du portefeuille</p>
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
                    <th>Commande</th>
                    <th>Entreprise</th>
                    <th>Montant</th>
                    <th>Statut paiement</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
            @forelse($commandes as $cmd)
                <tr>
                    <td>#{{ $cmd->ref ?? $cmd->id }}</td>
                    <td>{{ $cmd->employe?->entreprise?->nom ?? '—' }}</td>
                    <td><strong style="color: #FF0000;">{{ number_format($cmd->montant_total, 0, ',', ' ') }} F</strong></td>
                    <td><span class="badge badge-danger">{{ $cmd->statut_paiement }}</span></td>
                    <td>{{ $cmd->created_at?->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;padding:2rem;color:#666;">Aucun impayé</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding-top: 1rem;">{{ $commandes->links() }}</div>
</div>
@endsection
