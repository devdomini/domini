@extends('commercial.layout')

@section('title', 'Tableau de bord commercial')
@section('page-title', 'Tableau de bord')

@section('content')
<p style="color: #666; margin-bottom: 1.5rem;">{{ $stats['periode_mois'] ?? '' }}</p>

@if(($stats['impayes_count'] ?? 0) > 0)
<div class="commercial-alert">
    <strong>{{ $stats['impayes_count'] }} impayé(s)</strong>
    — {{ number_format($stats['impayes_montant'] ?? 0, 0, ',', ' ') }} FCFA
    <a href="{{ route('commercial.impayes.index') }}">Voir les impayés →</a>
</div>
@endif

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-label">Entreprises</div>
        <div class="stat-value">{{ $stats['entreprises_count'] }}</div>
        <div class="stat-change">{{ $stats['entreprises_actives'] ?? 0 }} actives</div>
    </div>
    <div class="stat-card yellow">
        <div class="stat-label">Employés</div>
        <div class="stat-value">{{ $stats['employes_count'] }}</div>
        <div class="stat-change">{{ $stats['employes_actifs'] ?? 0 }} actifs</div>
    </div>
    <div class="stat-card dark">
        <div class="stat-label">Boxes</div>
        <div class="stat-value">{{ $stats['boxes_count'] }}</div>
        <div class="stat-change">Installées</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-label">Casiers</div>
        <div class="stat-value">{{ $stats['casiers_count'] }}</div>
        <div class="stat-change">{{ $stats['casiers_libres'] ?? 0 }} libres · {{ $stats['taux_occupation_casiers'] ?? 0 }} % occupés</div>
    </div>
</div>

<p class="commercial-section-title">Abonnements</p>
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Actifs</div>
        <div class="stat-value">{{ $stats['abonnements_actifs'] }}</div>
        <div class="stat-change">sur {{ $stats['abonnements_total'] ?? 0 }}</div>
    </div>
    <div class="stat-card yellow">
        <div class="stat-label">Expirés</div>
        <div class="stat-value">{{ $stats['abonnements_expires'] }}</div>
    </div>
    <div class="stat-card dark">
        <div class="stat-label">Expirent &lt; 30 j</div>
        <div class="stat-value">{{ $stats['abonnements_expirent_30j'] ?? 0 }}</div>
    </div>
</div>

<p class="commercial-section-title">Commandes — {{ $stats['periode_mois'] ?? '' }}</p>
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Ce mois</div>
        <div class="stat-value">{{ $stats['commandes_mois_count'] ?? 0 }}</div>
        <div class="stat-change">{{ number_format($stats['commandes_mois_montant'] ?? 0, 0, ',', ' ') }} F</div>
    </div>
    <div class="stat-card yellow">
        <div class="stat-label">Aujourd'hui</div>
        <div class="stat-value">{{ $stats['commandes_aujourdhui_count'] ?? 0 }}</div>
        <div class="stat-change">{{ number_format($stats['commandes_aujourdhui_montant'] ?? 0, 0, ',', ' ') }} F</div>
    </div>
    <div class="stat-card dark">
        <div class="stat-label">En cours</div>
        <div class="stat-value">{{ $stats['commandes_en_cours_count'] ?? 0 }}</div>
        <div class="stat-change">En attente / confirmées</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-label">Impayées (mois)</div>
        <div class="stat-value">{{ $stats['commandes_mois_impayees_count'] ?? 0 }}</div>
        <div class="stat-change">{{ number_format($stats['commandes_mois_impayees_montant'] ?? 0, 0, ',', ' ') }} F</div>
    </div>
</div>

<p class="commercial-section-title">Finances — {{ $stats['periode_mois'] ?? '' }}</p>
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Encaissements</div>
        <div class="stat-value">{{ $stats['paiements_mois_count'] ?? 0 }}</div>
        <div class="stat-change">{{ number_format($stats['paiements_mois_montant'] ?? 0, 0, ',', ' ') }} F</div>
    </div>
    <div class="stat-card yellow">
        <div class="stat-label">Impayés (total)</div>
        <div class="stat-value">{{ $stats['impayes_count'] }}</div>
        <div class="stat-change">{{ number_format($stats['impayes_montant'] ?? 0, 0, ',', ' ') }} FCFA</div>
    </div>
</div>

<div class="commercial-actions" style="margin-top: 2rem;">
    <a href="{{ route('commercial.entreprises.create') }}" class="btn btn-primary">+ Nouvelle entreprise</a>
    <a href="{{ route('commercial.impayes.index') }}" class="btn btn-secondary">Voir les impayés</a>
</div>
@endsection
