@extends('admin.layout')

@section('title', 'Gestion du Menu')
@section('page-title', 'Gestion du Menu')

@section('content')
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: #3A3A3A; margin-bottom: 0.5rem;">Menu Domini</h2>
        <p style="color: #666;">Gérez les catégories et les plats disponibles sur la plateforme</p>
    </div>

    <!-- Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 3rem;">
        <div style="background: linear-gradient(135deg, #D9542A, #c13d18); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Total Catégories</div>
            <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ $totalCategories ?? 0 }}</div>
        </div>
        <div style="background: linear-gradient(135deg, #F7B801, #e5a900); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Total Plats</div>
            <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ $totalPlats ?? 0 }}</div>
        </div>
        <div style="background: linear-gradient(135deg, #3A3A3A, #2A2A2A); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Plats Actifs</div>
            <div style="font-size: 2rem; font-weight: 900; margin: 0.5rem 0;">{{ $platsActifs ?? 0 }}</div>
        </div>
    </div>

    <!-- Management Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem;">
        
        <!-- Gestion des Catégories -->
        <a href="{{ route('admin.menu.categories.index') }}" style="text-decoration: none;">
            <div class="card" style="height: 100%; transition: all 0.3s; cursor: pointer; border: 2px solid transparent;">
                <div style="padding: 2rem;">
                    <!-- Icon -->
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #D9542A, #F7B801); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                        <svg style="width: 40px; height: 40px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                    </div>

                    <!-- Content -->
                    <h3 style="font-size: 1.5rem; font-weight: 800; color: #3A3A3A; margin-bottom: 1rem;">
                        Gestion des Catégories
                    </h3>
                    <p style="color: #666; line-height: 1.6; margin-bottom: 2rem;">
                        Créez et organisez les catégories de plats : Entrées, Plats principaux, Desserts, Boissons, etc.
                    </p>

                    <!-- Features -->
                    <ul style="list-style: none; padding: 0; margin-bottom: 2rem;">
                        <li style="display: flex; align-items: center; gap: 0.5rem; color: #666; font-size: 0.875rem; margin-bottom: 0.5rem;">
                            <svg style="width: 16px; height: 16px; color: #D9542A;" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Créer des catégories
                        </li>
                        <li style="display: flex; align-items: center; gap: 0.5rem; color: #666; font-size: 0.875rem; margin-bottom: 0.5rem;">
                            <svg style="width: 16px; height: 16px; color: #D9542A;" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Modifier l'ordre d'affichage
                        </li>
                        <li style="display: flex; align-items: center; gap: 0.5rem; color: #666; font-size: 0.875rem;">
                            <svg style="width: 16px; height: 16px; color: #D9542A;" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Activer/Désactiver
                        </li>
                    </ul>

                    <!-- CTA -->
                    <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 1.5rem; border-top: 1px solid #E5E5E5;">
                        <span style="font-weight: 600; color: #D9542A;">Gérer les catégories</span>
                        <svg style="width: 20px; height: 20px; color: #D9542A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </div>
        </a>

        <!-- Gestion des Plats -->
        <a href="{{ route('admin.menu.plats.index') }}" style="text-decoration: none;">
            <div class="card" style="height: 100%; transition: all 0.3s; cursor: pointer; border: 2px solid transparent;">
                <div style="padding: 2rem;">
                    <!-- Icon -->
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #F7B801, #D9542A); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                        <svg style="width: 40px; height: 40px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>

                    <!-- Content -->
                    <h3 style="font-size: 1.5rem; font-weight: 800; color: #3A3A3A; margin-bottom: 1rem;">
                        Gestion des Plats
                    </h3>
                    <p style="color: #666; line-height: 1.6; margin-bottom: 2rem;">
                        Ajoutez et gérez tous les plats disponibles avec photos, prix, descriptions et disponibilité.
                    </p>

                    <!-- Features -->
                    <ul style="list-style: none; padding: 0; margin-bottom: 2rem;">
                        <li style="display: flex; align-items: center; gap: 0.5rem; color: #666; font-size: 0.875rem; margin-bottom: 0.5rem;">
                            <svg style="width: 16px; height: 16px; color: #F7B801;" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Ajouter des plats avec photos
                        </li>
                        <li style="display: flex; align-items: center; gap: 0.5rem; color: #666; font-size: 0.875rem; margin-bottom: 0.5rem;">
                            <svg style="width: 16px; height: 16px; color: #F7B801;" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Définir les prix (1500-3000 FCFA)
                        </li>
                        <li style="display: flex; align-items: center; gap: 0.5rem; color: #666; font-size: 0.875rem;">
                            <svg style="width: 16px; height: 16px; color: #F7B801;" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Gérer la disponibilité
                        </li>
                    </ul>

                    <!-- CTA -->
                    <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 1.5rem; border-top: 1px solid #E5E5E5;">
                        <span style="font-weight: 600; color: #F7B801;">Gérer les plats</span>
                        <svg style="width: 20px; height: 20px; color: #F7B801;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <style>
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
            border-color: #D9542A !important;
        }
    </style>
@endsection
