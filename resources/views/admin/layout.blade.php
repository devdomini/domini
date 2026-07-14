<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Domini</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --domini-red: #FF0000;
            --domini-red-dark: #CC0000;
            --domini-red-deep: #990000;
            --domini-black: #000000;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #FDFBF8;
            color: #000000;
            line-height: 1.6;
        }

        /* Sidebar : colonne flex + menu scrollable (tous les onglets accessibles sans zoom) */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 260px;
            max-height: 100vh;
            background-color: #000000;
            padding: 2rem 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-sizing: border-box;
        }

        .sidebar-header {
            flex-shrink: 0;
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid #333;
        }

        .sidebar-logo {
            font-size: 1.5rem;
            font-weight: 900;
            color: #FFFFFF;
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .sidebar-nav {
            flex: 1;
            min-height: 0;
            padding: 1rem 0 2rem;
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: #2C2C2C;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: #555;
            border-radius: 3px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: #666;
        }

        .sidebar-nav ul {
            list-style: none;
        }

        .sidebar-nav li {
            margin-bottom: 0.5rem;
        }

        .sidebar-group {
            margin-bottom: 1.15rem;
        }

        .sidebar-group:last-of-type {
            margin-bottom: 0;
        }

        .sidebar-group-title {
            padding: 0.35rem 1.5rem 0.5rem;
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #6b6b6b;
            user-select: none;
        }

        .sidebar-group ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            color: #999;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }

        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background-color: #2C2C2C;
            color: #FFFFFF;
        }

        .sidebar-nav a.active {
            border-left: 3px solid #FF0000;
        }

        .sidebar-icon {
            width: 20px;
            height: 20px;
        }

        /* Top Bar */
        .topbar {
            position: fixed;
            top: 0;
            left: 260px;
            right: 0;
            height: 70px;
            background-color: #FFFFFF;
            border-bottom: 1px solid #E5E5E5;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            z-index: 999;
        }

        .topbar-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1A1A1A;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.875rem;
        }

        .user-role {
            font-size: 0.75rem;
            color: #666;
        }

        .logout-btn {
            padding: 0.5rem 1rem;
            background-color: #FF0000;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
        }

        .logout-btn:hover {
            background-color: #CC0000;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            margin-top: 70px;
            padding: 2rem;
            min-height: calc(100vh - 70px);
        }

        .content-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Cards */
        .card {
            background-color: #FFFFFF;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .card-header {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #E5E5E5;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1A1A1A;
        }

        .card-body {
            padding: 0;
        }

        /* Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, #FF0000, #CC0000);
            border-radius: 12px;
            padding: 1.5rem;
            color: white;
        }

        .stat-card.orange {
            background: linear-gradient(135deg, #FF0000, #CC0000);
        }

        .stat-card.yellow {
            background: linear-gradient(135deg, #CC0000, #990000);
        }

        .stat-card.dark {
            background: linear-gradient(135deg, #1A1A1A, #000000);
        }

        .stat-label {
            font-size: 0.875rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 900;
            margin-bottom: 0.5rem;
        }

        .stat-change {
            font-size: 0.75rem;
            opacity: 0.9;
        }

        /* Table */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #E5E5E5;
        }

        th {
            font-weight: 600;
            color: #666;
            font-size: 0.875rem;
            text-transform: uppercase;
        }

        td {
            color: #1A1A1A;
        }

        tr:hover {
            background-color: #F9F9F9;
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-success {
            background-color: #FFEBEE;
            color: #CC0000;
        }

        .badge-warning {
            background-color: #FFF3E0;
            color: #E65100;
        }

        .badge-danger {
            background-color: #FFEBEE;
            color: #CC0000;
        }

        .badge-info {
            background-color: #E3F2FD;
            color: #1976D2;
        }

        /* Buttons */
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            text-decoration: none;
            display: inline-block;
        }

        /* Small icon buttons (used across admin tables) */
        .btn-icon {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.12s ease, filter 0.12s ease, opacity 0.12s ease;
            flex-shrink: 0;
        }

        .btn-icon:hover {
            filter: brightness(0.98);
            transform: translateY(-1px);
        }

        .btn-icon:active {
            transform: translateY(0px);
            filter: brightness(0.95);
        }

        .btn-icon[disabled] {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Inputs / selects baseline for consistent height */
        input[type="text"], input[type="search"], select {
            font-family: inherit;
        }

        .btn-primary {
            background-color: #FF0000;
            color: white;
        }

        .btn-primary:hover {
            background-color: #CC0000;
        }

        .btn-secondary {
            background-color: #E5E5E5;
            color: #1A1A1A;
        }

        .btn-secondary:hover {
            background-color: #D5D5D5;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }

            .topbar,
            .main-content {
                margin-left: 0;
                left: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    @yield('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="{{ route(auth()->user()->role === 'commercial' ? 'commercial.dashboard' : 'admin.dashboard') }}" class="sidebar-logo">Domini</a>
        </div>
        
        <nav class="sidebar-nav">
            @if(auth()->user()->role === 'commercial')
                @include('commercial.partials.sidebar')
            @else
            <div class="sidebar-group">
                <div class="sidebar-group-title">Général</div>
                <ul>
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.campagnes.index') }}" class="{{ request()->routeIs('admin.campagnes.*') ? 'active' : '' }}">
                            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            Campagnes
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.support.index') }}" class="{{ request()->routeIs('admin.support.*') ? 'active' : '' }}">
                            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Support
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sidebar-group">
                <div class="sidebar-group-title">Organisation</div>
                <ul>
                    <li>
                        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Utilisateurs
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.entreprises.index') }}" class="{{ request()->routeIs('admin.entreprises.*') ? 'active' : '' }}">
                            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Entreprises
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.warehouses.index') }}" class="{{ request()->routeIs('admin.warehouses.*') ? 'active' : '' }}">
                            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Entrepôts
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.communes.index') }}" class="{{ request()->routeIs('admin.communes.*') ? 'active' : '' }}">
                            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Communes
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.trajets.index') }}" class="{{ request()->routeIs('admin.trajets.*') ? 'active' : '' }}">
                            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            Trajets
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sidebar-group">
                <div class="sidebar-group-title">Catalogue & ventes</div>
                <ul>
                    <li>
                        <a href="{{ route('admin.menu.index') }}" class="{{ request()->routeIs('admin.menu.*') ? 'active' : '' }}">
                            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Menu
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.commandes.index') }}" class="{{ request()->routeIs('admin.commandes.*') ? 'active' : '' }}">
                            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            Commandes
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.abonnements.index') }}" class="{{ request()->routeIs('admin.abonnements.*') ? 'active' : '' }}">
                            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Abonnements
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.paiements.index') }}" class="{{ request()->routeIs('admin.paiements.*') ? 'active' : '' }}">
                            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Paiements
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sidebar-group">
                <div class="sidebar-group-title">Livraison & logistique</div>
                <ul>
                    <li>
                        <a href="{{ route('admin.livreurs.index') }}" class="{{ request()->routeIs('admin.livreurs.*') ? 'active' : '' }}">
                            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                            </svg>
                            Livreurs
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.livraisons.index') }}" class="{{ request()->routeIs('admin.livraisons.*') ? 'active' : '' }}">
                            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                            </svg>
                            Livraisons
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.traffic-livreur.index') }}" class="{{ request()->routeIs('admin.traffic-livreur.*') ? 'active' : '' }}">
                            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            Traffic livreur
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.boxes.index') }}" class="{{ request()->routeIs('admin.boxes.*') ? 'active' : '' }}">
                            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Boxes & Casiers
                        </a>
                    </li>
                </ul>
            </div>
            @endif
        </nav>
    </aside>

    <!-- Top Bar -->
    <header class="topbar">
        <h1 class="topbar-title">@yield('page-title', 'Dashboard')</h1>
        
        <div class="topbar-user">
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div class="user-role">{{ auth()->user()->role ?? 'Administrateur' }}</div>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Déconnexion</button>
            </form>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="content-container">
            @if(session('success'))
                <div style="background-color: #E8F5E9; color: #2d9248; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="background-color: #FFEBEE; color: #CC0000; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    @yield('scripts')
</body>
</html>
