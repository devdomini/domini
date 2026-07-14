@extends('admin.layout')

@section('title', 'Dashboard')
@section('page-title', 'Tableau de Bord')

@section('content')
    <!-- KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <!-- Total Employés -->
        <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); position: relative; overflow: hidden;">
            <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: linear-gradient(135deg, #FF0000, #CC0000); opacity: 0.1; border-radius: 50%;"></div>
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                <div>
                    <div style="font-size: 0.875rem; color: #666; font-weight: 500;">Total Employés</div>
                    <div style="font-size: 2.5rem; font-weight: 900; color: #FF0000; margin-top: 0.5rem;">{{ number_format($stats['total_employes']) }}</div>
                </div>
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #FF0000, #CC0000); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 24px; height: 24px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                @if($stats['employes_change'] >= 0)
                    <span style="color: #10B981; font-weight: 600; font-size: 0.875rem;">↗ +{{ $stats['employes_change'] }}%</span>
                @else
                    <span style="color: #EF4444; font-weight: 600; font-size: 0.875rem;">↘ {{ $stats['employes_change'] }}%</span>
                @endif
                <span style="color: #999; font-size: 0.75rem;">vs mois dernier</span>
            </div>
        </div>

        <!-- Commandes Aujourd'hui -->
        <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); position: relative; overflow: hidden;">
            <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: linear-gradient(135deg, #CC0000, #990000); opacity: 0.1; border-radius: 50%;"></div>
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                <div>
                    <div style="font-size: 0.875rem; color: #666; font-weight: 500;">Commandes Aujourd'hui</div>
                    <div style="font-size: 2.5rem; font-weight: 900; color: #CC0000; margin-top: 0.5rem;">{{ $stats['commandes_aujourd_hui'] }}</div>
                </div>
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #CC0000, #990000); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 24px; height: 24px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                @if($stats['commandes_change'] >= 0)
                    <span style="color: #10B981; font-weight: 600; font-size: 0.875rem;">↗ +{{ $stats['commandes_change'] }}%</span>
                @else
                    <span style="color: #EF4444; font-weight: 600; font-size: 0.875rem;">↘ {{ $stats['commandes_change'] }}%</span>
                @endif
                <span style="color: #999; font-size: 0.75rem;">vs hier</span>
            </div>
        </div>

        <!-- Entreprises Actives -->
        <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); position: relative; overflow: hidden;">
            <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: linear-gradient(135deg, #10B981, #059669); opacity: 0.1; border-radius: 50%;"></div>
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                <div>
                    <div style="font-size: 0.875rem; color: #666; font-weight: 500;">Entreprises Actives</div>
                    <div style="font-size: 2.5rem; font-weight: 900; color: #10B981; margin-top: 0.5rem;">{{ $stats['entreprises_actives'] }}</div>
                </div>
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #10B981, #059669); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 24px; height: 24px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="color: #10B981; font-weight: 600; font-size: 0.875rem;">+{{ $stats['entreprises_nouvelles'] }}</span>
                <span style="color: #999; font-size: 0.75rem;">nouvelles ce mois</span>
            </div>
        </div>

        <!-- Chiffre d'Affaires -->
        <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); position: relative; overflow: hidden;">
            <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: linear-gradient(135deg, #1A1A1A, #000000); opacity: 0.1; border-radius: 50%;"></div>
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                <div>
                    <div style="font-size: 0.875rem; color: #666; font-weight: 500;">Chiffre d'Affaires</div>
                    <div style="font-size: 1.75rem; font-weight: 900; color: #000000; margin-top: 0.5rem;">{{ number_format($stats['chiffre_affaires'], 0, ',', ' ') }} <span style="font-size: 1rem;">FCFA</span></div>
                </div>
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #1A1A1A, #000000); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 24px; height: 24px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                @if($stats['ca_change'] >= 0)
                    <span style="color: #10B981; font-weight: 600; font-size: 0.875rem;">↗ +{{ $stats['ca_change'] }}%</span>
                @else
                    <span style="color: #EF4444; font-weight: 600; font-size: 0.875rem;">↘ {{ $stats['ca_change'] }}%</span>
                @endif
                <span style="color: #999; font-size: 0.75rem;">vs mois dernier</span>
            </div>
        </div>
    </div>



        <div class="card-body" style="padding: 1.5rem;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <a href="{{ route('admin.commandes.index') }}" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: linear-gradient(135deg, #FF0000, #CC0000); color: white; border-radius: 12px; text-decoration: none; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 1.125rem;">Commandes</div>
                        <div style="font-size: 0.875rem; opacity: 0.9;">Gérer les commandes</div>
                    </div>
                </a>

                <a href="{{ route('admin.menu.plats.index') }}" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: linear-gradient(135deg, #CC0000, #990000); color: white; border-radius: 12px; text-decoration: none; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 1.125rem;">Nouveau Plat</div>
                        <div style="font-size: 0.875rem; opacity: 0.9;">Ajouter au menu</div>
                    </div>
                </a>

                <a href="{{ route('admin.entreprises.create') }}" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: linear-gradient(135deg, #10B981, #059669); color: white; border-radius: 12px; text-decoration: none; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 1.125rem;">Entreprise</div>
                        <div style="font-size: 0.875rem; opacity: 0.9;">Ajouter entreprise</div>
                    </div>
                </a>

                <a href="{{ route('admin.livraisons.index') }}" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: linear-gradient(135deg, #1A1A1A, #000000); color: white; border-radius: 12px; text-decoration: none; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                        </svg>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 1.125rem;">Livraisons</div>
                        <div style="font-size: 0.875rem; opacity: 0.9;">Suivre les livraisons</div>
                    </div>
                </a>

                <a href="{{ route('admin.livreurs.index') }}" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: linear-gradient(135deg, #6366F1, #4F46E5); color: white; border-radius: 12px; text-decoration: none; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 1.125rem;">Livreurs</div>
                        <div style="font-size: 0.875rem; opacity: 0.9;">Gérer les livreurs</div>
                    </div>
                </a>

                <a href="{{ route('admin.paiements.index') }}" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: linear-gradient(135deg, #EC4899, #DB2777); color: white; border-radius: 12px; text-decoration: none; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 1.125rem;">Paiements</div>
                        <div style="font-size: 0.875rem; opacity: 0.9;">Suivi financier</div>
                    </div>
                </a>
            </div>
        </div>
    
    <!-- Charts Row -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
        <!-- Evolution des commandes (7 derniers jours) -->
        <div class="card" style="background: white; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div class="card-header" style="padding: 1.5rem; border-bottom: 1px solid #E5E5E5;">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #000000; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    @include('admin.partials.icon', ['name' => 'chart-line', 'size' => 20])
                    Évolution des Commandes (7 jours)
                </h3>
            </div>
            <div class="card-body" style="padding: 1.5rem;">
                <canvas id="commandesChart" style="max-height: 300px;"></canvas>
            </div>
        </div>

        <!-- Répartition par statut (Donut) -->
        <div class="card" style="background: white; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div class="card-header" style="padding: 1.5rem; border-bottom: 1px solid #E5E5E5;">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #000000; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    @include('admin.partials.icon', ['name' => 'chart-pie', 'size' => 20])
                    Répartition des Commandes
                </h3>
            </div>
            <div class="card-body" style="padding: 1.5rem; display: flex; justify-content: center;">
                <canvas id="statutsChart" style="max-height: 300px; max-width: 300px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Histogram Row -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
        <!-- Top Entreprises (Bar Chart) -->
        <div class="card" style="background: white; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div class="card-header" style="padding: 1.5rem; border-bottom: 1px solid #E5E5E5;">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #000000; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    @include('admin.partials.icon', ['name' => 'chart-bar', 'size' => 20])
                    Top 10 Entreprises (Nombre de Commandes)
                </h3>
            </div>
            <div class="card-body" style="padding: 1.5rem;">
                <canvas id="entreprisesChart" style="max-height: 350px;"></canvas>
            </div>
        </div>

        <!-- Quick Stats -->
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <!-- Taux de livraison -->
            <div class="card" style="background: white; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 1.5rem;">
                <div style="font-size: 0.875rem; color: #666; margin-bottom: 0.5rem;">Taux de Livraison</div>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="flex: 1; height: 8px; background: #E5E5E5; border-radius: 4px; overflow: hidden;">
                        <div style="height: 100%; background: linear-gradient(90deg, #10B981, #059669); width: {{ $stats['taux_livraison'] }}%;"></div>
                    </div>
                    <span style="font-size: 1.25rem; font-weight: 700; color: #10B981;">{{ $stats['taux_livraison'] }}%</span>
                </div>
            </div>

            <!-- Panier moyen -->
            <div class="card" style="background: white; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 1.5rem;">
                <div style="font-size: 0.875rem; color: #666; margin-bottom: 0.5rem;">Panier Moyen</div>
                <div style="font-size: 2rem; font-weight: 900; color: #FF0000;">{{ number_format($stats['panier_moyen'], 0, ',', ' ') }} <span style="font-size: 1rem; color: #666;">FCFA</span></div>
            </div>

            <!-- Livreurs actifs -->
            <div class="card" style="background: white; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 1.5rem;">
                <div style="font-size: 0.875rem; color: #666; margin-bottom: 0.5rem;">Livreurs Actifs</div>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="font-size: 2rem; font-weight: 900; color: #CC0000;">{{ $stats['livreurs_actifs'] }}</div>
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #CC0000, #990000); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 24px; height: 24px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Rapides -->
   
@endsection

@section('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
// Configuration des couleurs Domini
const dominiColors = {
    primary: '#FF0000',
    secondary: '#CC0000',
    success: '#10B981',
    dark: '#000000',
    gray: '#666666'
};

// 1. Graphique d'évolution des commandes (7 derniers jours)
const commandesCtx = document.getElementById('commandesChart').getContext('2d');
new Chart(commandesCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($charts['commandes_evolution']['labels']) !!},
        datasets: [{
            label: 'Commandes',
            data: {!! json_encode($charts['commandes_evolution']['data']) !!},
            borderColor: dominiColors.primary,
            backgroundColor: dominiColors.primary + '20',
            tension: 0.4,
            fill: true,
            pointBackgroundColor: dominiColors.primary,
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: dominiColors.dark,
                padding: 12,
                cornerRadius: 8,
                titleFont: {
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    size: 13
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                },
                grid: {
                    color: '#E5E5E5'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

// 2. Graphique Donut - Répartition par statut
const statutsCtx = document.getElementById('statutsChart').getContext('2d');
new Chart(statutsCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($charts['statuts_repartition']['labels']) !!},
        datasets: [{
            data: {!! json_encode($charts['statuts_repartition']['data']) !!},
            backgroundColor: [
                '#FFA500',
                '#10B981',
                '#EF4444',
                '#3B82F6'
            ],
            borderWidth: 0,
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: {
                        size: 12,
                        weight: '600'
                    },
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                backgroundColor: dominiColors.dark,
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        },
        cutout: '70%'
    }
});

// 3. Graphique Bar - Top Entreprises
const entreprisesCtx = document.getElementById('entreprisesChart').getContext('2d');
new Chart(entreprisesCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($charts['top_entreprises']['labels']) !!},
        datasets: [{
            label: 'Nombre de commandes',
            data: {!! json_encode($charts['top_entreprises']['data']) !!},
            backgroundColor: dominiColors.secondary,
            borderRadius: 8,
            borderSkipped: false,
            hoverBackgroundColor: dominiColors.primary
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: dominiColors.dark,
                padding: 12,
                cornerRadius: 8
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                },
                grid: {
                    color: '#E5E5E5'
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    maxRotation: 45,
                    minRotation: 45
                }
            }
        }
    }
});
</script>
@endsection
