<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Menu - Domini</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Playfair+Display:wght@400;500;600;700;800;900&family=Cormorant+Garamond:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Cormorant Garamond', 'Georgia', serif;
                background-color: #F5F5F5;
                color: #2C2C2C;
                line-height: 1.8;
                overflow-x: hidden;
            }

            /* Navigation */
            nav {
                position: fixed;
                top: 0;
                width: 100%;
                background-color: #F5F5F5;
                border-bottom: 0px solid #E5E5E5;
                z-index: 1000;
                height: 80px;
            }

            .nav-content {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 1.25rem 3rem;
                max-width: 1400px;
                margin: 0 auto;
            }

            .logo {
                font-size: 2rem;
                font-weight: 900;
                color: #1A1A1A;
                letter-spacing: -0.5px;
                font-family: 'Inter', sans-serif;
            }

            .nav-right {
                display: flex;
                gap: 2rem;
                align-items: center;
            }

            .nav-link {
                text-decoration: none;
                color: #1A1A1A;
                font-weight: 500;
                font-size: 1rem;
                transition: all 0.3s ease;
                font-family: 'Inter', sans-serif;
            }

            .nav-link:hover {
                color: #D9542A;
                transform: translateY(-2px);
            }

            .nav-link.active {
                color: #D9542A;
                font-weight: 600;
            }

            .btn-signup {
                background-color: #2C2C2C;
                color: #FFFFFF;
                padding: 0.75rem 1.5rem;
                border-radius: 8px;
                text-decoration: none;
                font-weight: 600;
                font-size: 1rem;
                transition: all 0.3s ease;
                border: none;
                cursor: pointer;
                display: inline-block;
                font-family: 'Inter', sans-serif;
            }

            .btn-signup:hover {
                background-color: #D9542A;
                transform: translateY(-2px);
            }

            .menu-icon {
                display: flex;
                flex-direction: column;
                gap: 4px;
                cursor: pointer;
                padding: 0.5rem;
                border-radius: 8px;
                transition: all 0.3s ease;
            }

            .menu-icon:hover {
                background-color: rgba(217, 84, 42, 0.1);
            }

            .menu-icon span {
                width: 24px;
                height: 2px;
                background-color: #1A1A1A;
                display: block;
                transition: all 0.3s ease;
            }

            .menu-icon:hover span {
                background-color: #D9542A;
            }

            /* Hero Section avec Pattern */
            .menu-hero {
                margin-top: 80px;
                height: 300px;
                position: relative;
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: #D9542A;
                border-bottom: 3px solid #F7B801;
            }

            .menu-hero-pattern {
                position: absolute;
                top: 0;
                left: 0;
                width: 50%;
                height: 100%;
                opacity: 0.3;
                background-color: #F7B801;
            }

            .menu-hero-image {
                position: absolute;
                top: 0;
                right: 0;
                width: 50%;
                height: 100%;
                overflow: hidden;
                opacity: 0.6;
            }

            .menu-hero-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            /* Section Titre */
            .menu-title-section {
                background-color: #FFFFFF;
                padding: 6rem 3rem 4rem;
                text-align: center;
                position: relative;
                border-bottom: 3px solid #D9542A;
            }

            .menu-title-section::before {
                content: '';
                position: absolute;
                top: 0;
                left: 50%;
                transform: translateX(-50%);
                width: 150px;
                height: 3px;
                background-color: #D9542A;
            }

            .menu-title-section::after {
                content: '✦';
                position: absolute;
                top: 2rem;
                left: 50%;
                transform: translateX(-50%);
                font-size: 1.5rem;
                color: #D9542A;
            }

            .menu-title {
                font-size: 5rem;
                font-weight: 700;
                color: #2C2C2C;
                margin-bottom: 2rem;
                letter-spacing: 4px;
                font-family: 'Playfair Display', serif;
                text-transform: uppercase;
                position: relative;
            }

            .menu-divider {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 1.5rem;
                margin: 3rem 0;
            }

            .menu-divider-line {
                width: 120px;
                height: 1px;
                background-color: #F7B801;
            }

            .menu-divider-dots {
                display: flex;
                gap: 0.75rem;
                align-items: center;
            }

            .menu-divider-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background-color: #D9542A;
            }

            .menu-divider-dots::before,
            .menu-divider-dots::after {
                content: '❦';
                font-size: 1.25rem;
                color: #F7B801;
            }

            .menu-description {
                max-width: 800px;
                margin: 0 auto;
                font-size: 1.2rem;
                line-height: 2;
                color: #666666;
                font-weight: 400;
                font-family: 'Cormorant Garamond', serif;
                font-style: italic;
            }

            .menu-description strong {
                color: #2C2C2C;
                font-weight: 600;
                font-style: normal;
            }

            /* Section Menu Cards */
            .menu-content {
                background-color: #FFFFFF;
                padding: 5rem 3rem 7rem;
                position: relative;
            }

            .menu-container {
                max-width: 100%;
                margin: 0 auto;
            }

            /* Grid des catégories */
            .categories-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
                gap: 0;
                max-width: 1400px;
                margin: 0 auto;
            }

            .category-card {
                position: relative;
                height: 400px;
                overflow: hidden;
                cursor: pointer;
                transition: all 0.4s ease;
            }

            .category-card-image {
                width: 100%;
                height: 100%;
                position: relative;
            }

            .category-card-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.6s ease;
            }

            .category-card:hover .category-card-image img {
                transform: scale(1.1);
            }

            .category-card-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(to bottom, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.7) 100%);
                transition: all 0.4s ease;
            }

            .category-card:hover .category-card-overlay {
                background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.8) 100%);
            }

            .category-card-content {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                padding: 2.5rem;
                color: #FFFFFF;
                z-index: 2;
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
            }

            .category-card-title {
                font-size: 2.5rem;
                font-weight: 700;
                font-family: 'Playfair Display', serif;
                margin: 0;
                line-height: 1.2;
            }

            .category-card-btn {
                background-color: transparent;
                color: #FFFFFF;
                border: 2px solid #FFFFFF;
                padding: 0.75rem 2rem;
                border-radius: 50px;
                font-size: 1rem;
                font-weight: 600;
                font-family: 'Inter', sans-serif;
                cursor: pointer;
                transition: all 0.3s ease;
                width: fit-content;
            }

            .category-card-btn:hover {
                background-color: #FFFFFF;
                color: #2C2C2C;
            }

            /* Fond orange pour les catégories sans image */
            .category-card-orange-bg {
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, #F7B801 0%, #F49C12 50%, #D9542A 100%);
                position: relative;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .category-card-orange-bg::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.5) 100%);
            }

            .category-card.no-image:hover .category-card-orange-bg {
                transform: scale(1.05);
                transition: transform 0.6s ease;
            }

            /* Page de détails de catégorie */
            .category-details-page {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: #F5F5F5;
                z-index: 2000;
                display: none;
                overflow: hidden;
            }

            .category-details-page.active {
                display: block;
            }

            .close-details-btn {
                position: absolute;
                top: 2rem;
                right: 2rem;
                width: 50px;
                height: 50px;
                background-color: #FFFFFF;
                border: none;
                border-radius: 50%;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
                z-index: 10;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            }

            .close-details-btn:hover {
                background-color: #D9542A;
                color: #FFFFFF;
                transform: rotate(90deg);
            }

            .close-details-btn svg {
                stroke: currentColor;
            }

            .category-details-container {
                display: grid;
                grid-template-columns: 1fr 45%;
                height: 100%;
                gap: 0;
            }

            .category-details-left {
                padding: 5rem 4rem;
                overflow-y: auto;
                background-color: #FFFFFF;
            }

            .category-details-title {
                font-size: 3.5rem;
                font-weight: 700;
                font-family: 'Playfair Display', serif;
                color: #2C2C2C;
                margin-bottom: 3rem;
                letter-spacing: 1px;
            }

            .category-details-items {
                display: flex;
                flex-direction: column;
                gap: 2rem;
            }

            .category-details-item {
                background-color: #FAFAFA;
                padding: 2rem;
                border-radius: 12px;
                transition: all 0.3s ease;
                border-left: 4px solid transparent;
            }

            .category-details-item:hover {
                border-left-color: #D9542A;
                transform: translateX(10px);
                background-color: #FFF;
                box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            }

            .category-details-item-header {
                display: flex;
                justify-content: space-between;
                align-items: baseline;
                margin-bottom: 1rem;
            }

            .category-details-item-name {
                font-size: 1.8rem;
                font-weight: 700;
                font-family: 'Playfair Display', serif;
                color: #2C2C2C;
                margin: 0;
            }

            .category-details-item-price {
                font-size: 1.8rem;
                font-weight: 900;
                font-family: 'Playfair Display', serif;
                color: #F7B801;
                white-space: nowrap;
            }

            .category-details-item-description {
                font-size: 1rem;
                color: #666666;
                line-height: 1.6;
                font-family: 'Inter', sans-serif;
                margin: 0;
            }

            .category-details-right {
                position: relative;
                overflow: hidden;
                background-color: #2C2C2C;
            }

            .category-details-right img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .category-details-orange-bg {
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, #F7B801 0%, #F49C12 50%, #D9542A 100%);
                position: relative;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .category-details-orange-bg::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: radial-gradient(circle at center, rgba(255,255,255,0.1) 0%, transparent 70%);
            }

            .category-details-empty {
                text-align: center;
                padding: 3rem;
                color: #999;
            }

            .category-details-empty h3 {
                font-size: 1.5rem;
                font-family: 'Playfair Display', serif;
                margin-bottom: 1rem;
            }

            .menu-items-grid {
                display: flex;
                flex-direction: column;
                gap: 0;
                max-width: 1200px;
                margin: 0 auto;
            }

            .menu-item-card {
                display: grid;
                grid-template-columns: 1fr 200px;
                gap: 2rem;
                align-items: center;
                background-color: #FFFFFF;
                padding: 2.5rem 3rem;
                border-bottom: 1px solid #E5E5E5;
                transition: all 0.4s ease, opacity 0.4s ease, transform 0.4s ease;
                position: relative;
            }

            .menu-item-card:last-child {
                border-bottom: none;
            }

            .menu-item-card:hover {
                background-color: #FAFAFA;
                transform: translateX(5px);
            }

            .menu-item-image {
                width: 200px;
                height: 200px;
                overflow: hidden;
                position: relative;
                background-color: #F5F5F5;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                border: 3px solid #F7B801;
                flex-shrink: 0;
            }

            .menu-item-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 50%;
            }

            /* Icône par défaut pour plats sans image */
            .no-image-placeholder {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 100%;
                height: 100%;
                background-color: #FFF8F0;
                border-radius: 50%;
            }

            .no-image-placeholder svg {
                width: 80px;
                height: 80px;
                color: #D9542A;
                opacity: 0.4;
            }

            .menu-item-badge {
                position: absolute;
                top: 10px;
                right: 10px;
                background-color: #D9542A;
                color: #FFFFFF;
                padding: 0.35rem 0.75rem;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 700;
                font-family: 'Inter', sans-serif;
                letter-spacing: 0.5px;
                text-transform: uppercase;
                z-index: 10;
            }

            .menu-item-content {
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
            }

            .menu-item-header {
                display: flex;
                align-items: baseline;
                gap: 1rem;
            }

            .menu-item-name {
                font-size: 2rem;
                font-weight: 700;
                color: #2C2C2C;
                margin: 0;
                font-family: 'Playfair Display', serif;
                letter-spacing: 0.5px;
                line-height: 1.2;
            }

            .menu-item-price {
                font-size: 2rem;
                font-weight: 900;
                color: #F7B801;
                white-space: nowrap;
                font-family: 'Playfair Display', serif;
            }

            .menu-item-description {
                font-size: 1rem;
                color: #666666;
                line-height: 1.6;
                margin: 0;
                font-family: 'Inter', sans-serif;
            }

            .menu-item-category {
                display: inline-block;
                background-color: #F7B801;
                color: #2C2C2C;
                padding: 0.25rem 0.75rem;
                border-radius: 4px;
                font-size: 0.85rem;
                font-weight: 600;
                font-family: 'Inter', sans-serif;
                margin-top: 0.5rem;
            }

            /* Responsive */
            @media (max-width: 992px) {
                .menu-title {
                    font-size: 3.5rem;
                    letter-spacing: 2px;
                }

                .menu-description {
                    font-size: 1.1rem;
                }

                .categories-grid {
                    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                }

                .category-card {
                    height: 350px;
                }

                .category-card-title {
                    font-size: 2rem;
                }

                .category-details-container {
                    grid-template-columns: 1fr 40%;
                }

                .category-details-left {
                    padding: 4rem 3rem;
                }

                .category-details-title {
                    font-size: 3rem;
                }
            }

            /* Tablette */
            @media (max-width: 768px) {
                .nav-content {
                    padding: 1rem 1.5rem;
                }

                .nav-right > *:not(.menu-icon):not(.btn-signup) {
                    display: none;
                }

                .logo {
                    font-size: 1.5rem;
                    letter-spacing: -0.5px;
                }

                .btn-signup {
                    padding: 0.625rem 1.25rem;
                    font-size: 0.85rem;
                }

                .menu-hero {
                    height: 250px;
                }

                .menu-hero-pattern,
                .menu-hero-image {
                    width: 100%;
                }

                .menu-hero-pattern {
                    opacity: 0.2;
                }

                .menu-title {
                    font-size: 2.5rem;
                    letter-spacing: 1px;
                }

                .menu-title-section {
                    padding: 4rem 1.5rem 3rem;
                }

                .menu-description {
                    font-size: 1.05rem;
                    line-height: 1.8;
                }

                .menu-divider-line {
                    width: 60px;
                }

                .menu-content {
                    padding: 3rem 1.5rem 5rem;
                }

                .categories-grid {
                    grid-template-columns: 1fr;
                    gap: 1rem;
                }

                .category-card {
                    height: 300px;
                }

                .category-card-content {
                    padding: 2rem;
                }

                .category-card-title {
                    font-size: 1.8rem;
                }

                .category-card-btn {
                    padding: 0.65rem 1.5rem;
                    font-size: 0.9rem;
                }

                /* Page de détails sur mobile */
                .category-details-container {
                    grid-template-columns: 1fr;
                    grid-template-rows: auto 1fr;
                }

                .category-details-left {
                    padding: 4rem 1.5rem 2rem;
                    order: 2;
                }

                .category-details-right {
                    order: 1;
                    height: 250px;
                }

                .category-details-title {
                    font-size: 2rem;
                    margin-bottom: 2rem;
                }

                .category-details-item {
                    padding: 1.5rem;
                }

                .category-details-item-header {
                    flex-direction: column;
                    gap: 0.5rem;
                    align-items: flex-start;
                }

                .category-details-item-name {
                    font-size: 1.5rem;
                }

                .category-details-item-price {
                    font-size: 1.5rem;
                }

                .close-details-btn {
                    top: 1rem;
                    right: 1rem;
                    width: 40px;
                    height: 40px;
                }
            }

            @media (min-width: 769px) {
                .menu-icon {
                    display: none;
                }
            }

            /* Animation d'apparition */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(40px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes ornamentSpin {
                from {
                    transform: rotate(0deg);
                }
                to {
                    transform: rotate(360deg);
                }
            }

            .menu-item-card {
                animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
                opacity: 0;
            }

            .menu-item-card:nth-child(1) { animation-delay: 0.1s; }
            .menu-item-card:nth-child(2) { animation-delay: 0.2s; }
            .menu-item-card:nth-child(3) { animation-delay: 0.3s; }
            .menu-item-card:nth-child(4) { animation-delay: 0.4s; }
            .menu-item-card:nth-child(5) { animation-delay: 0.5s; }
            .menu-item-card:nth-child(6) { animation-delay: 0.6s; }
            .menu-item-card:nth-child(7) { animation-delay: 0.7s; }
            .menu-item-card:nth-child(8) { animation-delay: 0.8s; }
            .menu-item-card:nth-child(9) { animation-delay: 0.9s; }
            .menu-item-card:nth-child(10) { animation-delay: 1s; }

            /* Message vide */
            .empty-message {
                grid-column: 1/-1;
                text-align: center;
                padding: 5rem 2rem;
                background-color: #FFFFFF;
                border: 2px dashed #D9542A;
                border-radius: 12px;
            }

            .empty-message h3 {
                font-size: 2rem;
                color: #2C2C2C;
                margin-bottom: 1rem;
                font-family: 'Playfair Display', serif;
            }

            .empty-message p {
                color: #666666;
                font-size: 1.1rem;
                font-family: 'Cormorant Garamond', serif;
                font-style: italic;
            }
        </style>
    </head>
    <body>
        <!-- Navigation -->
        <nav>
            <div class="nav-content">
                <a href="{{ url('/') }}" class="logo" style="text-decoration: none;">Domini</a>
                <div class="nav-right">
                    <a href="{{ url('/notre-menu') }}" class="nav-link active">Notre cuisine</a>
                    <a href="{{ url('/support') }}" class="nav-link">Support</a>
                    <a href="{{ url('/devenir-livreur') }}" class="nav-link">Devenir livreur</a>
                    <a href="#" class="nav-link">Telecharger l'application</a>
                    <a href="{{ url('/inscription') }}" class="btn-signup">Inscrivez votre entreprise</a>
                    <div class="menu-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero avec Pattern et Image -->
       

        <!-- Section Titre -->
        <section class="menu-title-section">
            <h1 class="menu-title">Notre carte</h1>
            
            <div class="menu-divider">
                <div class="menu-divider-line"></div>
                <div class="menu-divider-dots">
                    <span class="menu-divider-dot"></span>
                    <span class="menu-divider-dot"></span>
                    <span class="menu-divider-dot"></span>
                </div>
                <div class="menu-divider-line"></div>
            </div>

            <div class="menu-description">
                L'équipe de votre <strong>restaurant Domini</strong> est heureuse de vous présenter sa carte d'exception. Elle est élaborée selon les inspirations du moment, mais elle célèbre surtout les <strong>secrets de cuisine qui nous ont été transmis par nos chefs passionnés</strong>. Des <strong>mezzés froids et chauds</strong> à nos <strong>incontournables grillades</strong>, sans oublier les spécialités de la maison et les <strong>pâtisseries orientales</strong>, laissez-vous guider par votre gourmandise et laissez notre équipe ravir vos papilles !
            </div>
        </section>

        <!-- Section Menu - Grid de Catégories -->
        <section class="menu-content">
            <div class="menu-container">
                <!-- Grid des catégories avec images -->
                <div class="categories-grid">
                    @foreach($categories as $categorie)
                        <div class="category-card {{ (!$categorie->logo || !file_exists(public_path('storage/' . $categorie->logo))) ? 'no-image' : '' }}" onclick="showCategoryDetails({{ $categorie->id }}, '{{ $categorie->nom }}', '{{ $categorie->logo ?? '' }}')">
                            <div class="category-card-image">
                                @if($categorie->logo && file_exists(public_path('storage/' . $categorie->logo)))
                                    <img src="{{ asset('storage/' . $categorie->logo) }}" alt="{{ $categorie->nom }}">
                                    <div class="category-card-overlay"></div>
                                @else
                                    <div class="category-card-orange-bg"></div>
                                @endif
                            </div>
                            <div class="category-card-content">
                                <h3 class="category-card-title">{{ $categorie->nom }}</h3>
                                <button class="category-card-btn">En savoir plus</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Page de détails de catégorie (cachée par défaut) -->
        <div class="category-details-page" id="categoryDetailsPage">
            <button class="close-details-btn" onclick="closeCategoryDetails()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
            
            <div class="category-details-container">
                <!-- Gauche : Liste des plats -->
                <div class="category-details-left">
                    <h2 class="category-details-title" id="categoryDetailsTitle">Catégorie</h2>
                    <div class="category-details-items" id="categoryDetailsItems">
                        <!-- Les plats seront insérés ici dynamiquement -->
                    </div>
                </div>
                
                <!-- Droite : Image de la catégorie -->
                <div class="category-details-right" id="categoryDetailsRight">
                    <img id="categoryDetailsImage" src="" alt="Catégorie" style="display: none;">
                    <div id="categoryDetailsOrangeBg" class="category-details-orange-bg" style="display: none;"></div>
                </div>
            </div>
        </div>

        <script>
            // Données des plats (depuis le backend Laravel)
            const platsData = @json($plats);

            // Fonction pour afficher les détails d'une catégorie
            function showCategoryDetails(categoryId, categoryName, categoryImage) {
                const detailsPage = document.getElementById('categoryDetailsPage');
                const detailsTitle = document.getElementById('categoryDetailsTitle');
                const detailsItems = document.getElementById('categoryDetailsItems');
                const detailsImage = document.getElementById('categoryDetailsImage');
                const detailsOrangeBg = document.getElementById('categoryDetailsOrangeBg');

                // Filtrer les plats de cette catégorie
                const categoryPlats = platsData.filter(plat => plat.id_categorie === categoryId);

                // Mettre à jour le titre
                detailsTitle.textContent = categoryName;

                // Mettre à jour l'image ou le fond orange
                if (categoryImage && categoryImage !== '') {
                    detailsImage.src = `/storage/${categoryImage}`;
                    detailsImage.style.display = 'block';
                    detailsOrangeBg.style.display = 'none';
                } else {
                    detailsImage.style.display = 'none';
                    detailsOrangeBg.style.display = 'block';
                }

                // Afficher les plats
                if (categoryPlats.length > 0) {
                    detailsItems.innerHTML = categoryPlats.map(plat => `
                        <div class="category-details-item">
                            <div class="category-details-item-header">
                                <h3 class="category-details-item-name">${plat.nom}</h3>
                                <span class="category-details-item-price">${formatPrice(plat.prix)} FCFA</span>
                            </div>
                            <p class="category-details-item-description">
                                ${plat.detail || 'Délicieux plat préparé avec soin par nos chefs.'}
                            </p>
                        </div>
                    `).join('');
                } else {
                    detailsItems.innerHTML = `
                        <div class="category-details-empty">
                            <h3>Aucun plat disponible</h3>
                            <p>Cette catégorie ne contient pas encore de plats.</p>
                        </div>
                    `;
                }

                // Afficher la page de détails
                detailsPage.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            // Fonction pour fermer les détails
            function closeCategoryDetails() {
                const detailsPage = document.getElementById('categoryDetailsPage');
                detailsPage.classList.remove('active');
                document.body.style.overflow = 'auto';
            }

            // Fonction pour formater le prix
            function formatPrice(price) {
                return new Intl.NumberFormat('fr-FR', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(price);
            }

            // Fermer avec la touche Échap
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeCategoryDetails();
                }
            });
        </script>

        <!-- Smartsupp Live Chat script -->
        <script type="text/javascript">
        var _smartsupp = _smartsupp || {};
        _smartsupp.key = '73234f96a43e1e6223c9bc16cc051c9a054376c2';
        window.smartsupp||(function(d) {
          var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
          s=d.getElementsByTagName('script')[0];c=d.createElement('script');
          c.type='text/javascript';c.charset='utf-8';c.async=true;
          c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
        })(document);
        </script>
        <noscript>Powered by <a href="https://www.smartsupp.com" target="_blank">Smartsupp</a></noscript>
    </body>
</html>
