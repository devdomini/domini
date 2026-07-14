@extends('commercial.layout')

@section('title', 'Paiements')
@section('page-title', 'Paiements')

@section('content')
<div style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.5rem; font-weight: 700; color: #000000;">Paiements</h2>
    <p style="color: #666; margin-top: 0.25rem;">Encaissements du portefeuille commercial</p>
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
                    <th>Réf</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
            @forelse($paiements as $p)
                <tr>
                    <td>{{ $p->ref }}</td>
                    <td><strong style="color: #FF0000;">{{ number_format($p->montant, 0, ',', ' ') }} F</strong></td>
                    <td><span class="badge badge-success">{{ $p->statut }}</span></td>
                    <td>{{ $p->created_at?->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center;padding:2rem;color:#666;">Aucun paiement.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding-top: 1rem;">{{ $paiements->links() }}</div>
</div>
@endsection
