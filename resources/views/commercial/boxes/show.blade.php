@extends('commercial.layout')

@section('title', $box->nom)
@section('page-title', $box->nom.' — casiers')

@section('content')
<a href="{{ route('commercial.entreprises.show', $box->id_entreprise) }}" class="commercial-back-link">← Entreprise</a>

<div class="card" style="margin-bottom: 1.5rem;">
    <strong style="color: #FF0000;">{{ $box->ref }}</strong>
    <span class="commercial-muted"> — {{ $box->entreprise->nom }} — {{ $box->capacite }} casiers</span>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Réf</th>
                    <th>Statut</th>
                    <th>Employé</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @foreach($box->casiers as $c)
                <tr>
                    <td>{{ $c->numero_casier }}</td>
                    <td>{{ $c->ref }}</td>
                    <td><span class="badge badge-info">{{ $c->statut }}</span></td>
                    <td>{{ $c->employe?->name ?? '—' }}</td>
                    <td>
                        @if($c->statut !== 'hors_service')
                            @if($c->id_employe)
                                <form method="POST" action="{{ route('commercial.boxes.casiers.unassign', [$box->id, $c->id]) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary" style="padding: 0.35rem 0.65rem; font-size: 0.8rem;">Libérer</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('commercial.boxes.casiers.assign', [$box->id, $c->id]) }}" class="commercial-casier-row" style="margin:0;">
                                    @csrf
                                    <select name="employe_id" required style="padding: 0.4rem; border: 2px solid #E5E5E5; border-radius: 6px; max-width: 180px;">
                                        <option value="">Employé…</option>
                                        @foreach($box->entreprise->employes as $emp)
                                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-primary" style="padding: 0.35rem 0.65rem; font-size: 0.8rem;">Attribuer</button>
                                </form>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
