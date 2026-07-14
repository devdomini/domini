<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Support - Domini</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #FFFFFF;
            color: #1A1A1A;
            line-height: 1.6;
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
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        nav.scrolled {
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            background-color: rgba(245, 245, 245, 0.95);
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
        }

        .nav-right {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .language-selector {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #1A1A1A;
            font-weight: 500;
            cursor: pointer;
            padding: 0.5rem;
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 8px;
        }

        .language-selector:hover {
            background-color: rgba(255, 0, 0, 0.1);
            color: #FF0000;
        }

        .flag-icon {
            font-size: 1.25rem;
        }

        .nav-link {
            text-decoration: none;
            color: #1A1A1A;
            font-weight: 500;
            font-size: 1rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #FF0000;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-link:hover {
            color: #FF0000;
            transform: translateY(-2px);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .btn-signup {
            background-color: #000000;
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
            position: relative;
            overflow: hidden;
        }

        .btn-signup::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.3),
                transparent
            );
            transition: left 0.6s ease;
        }

        .btn-signup:hover::before {
            left: 100%;
        }

        .btn-signup:hover {
            background-color: #FF0000;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 0, 0, 0.3);
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
            background-color: rgba(255, 0, 0, 0.1);
        }

        .menu-icon span {
            width: 24px;
            height: 2px;
            background-color: #1A1A1A;
            display: block;
            transition: all 0.3s ease;
        }

        .menu-icon:hover span {
            background-color: #FF0000;
        }

        @media (max-width: 768px) {
            .nav-content {
                padding: 1rem 1.5rem;
            }

            .nav-right > *:not(.menu-icon):not(.btn-signup) {
                display: none;
            }
        }

        @media (min-width: 769px) {
            .menu-icon {
                display: none;
            }
        }

        /* Hero Section */
        .support-hero {
            padding: 10rem 2rem 4rem;
            background-color: #F5F5F5;
            text-align: center;
        }

        .support-hero h1 {
            font-size: 3.5rem;
            font-weight: 900;
            color: #1A1A1A;
            margin-bottom: 2.5rem;
            letter-spacing: -2px;
        }

        .search-container {
            max-width: 700px;
            margin: 0 auto;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 1.25rem 1.5rem 1.25rem 3.5rem;
            border: 2px solid #E5E5E5;
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
        }

        .search-input:focus {
            outline: none;
            border-color: #34A853;
        }

        .search-icon {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        /* Categories Section */
        .categories-section {
            padding: 4rem 2rem;
            background-color: #F5F5F5;
        }

        .categories-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        .category-card {
            background-color: #FFFFFF;
            padding: 2.5rem 2rem;
            border-radius: 12px;
            text-decoration: none;
            color: #1A1A1A;
            transition: all 0.3s;
            border: 2px solid #E5E5E5;
            cursor: pointer;
        }

        .category-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            border-color: #34A853;
        }

        .category-card.active {
            border-color: #34A853;
            background-color: #F0FFF4;
        }

        .category-card h3 {
            font-size: 1.25rem;
            font-weight: 700;
        }

        /* Category Content */
        .category-content-section {
            padding: 4rem 2rem;
            background-color: #FFFFFF;
        }

        .category-content {
            max-width: 1200px;
            margin: 0 auto;
            display: none;
        }

        .category-content.active {
            display: block;
        }

        .content-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .content-header h2 {
            font-size: 2.5rem;
            font-weight: 900;
            color: #1A1A1A;
            margin-bottom: 1rem;
            letter-spacing: -1px;
        }

        .content-header p {
            font-size: 1.125rem;
            color: #666;
            max-width: 700px;
            margin: 0 auto;
        }

        .content-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .content-card {
            background-color: #F5F5F5;
            padding: 2rem;
            border-radius: 12px;
        }

        .content-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #1A1A1A;
        }

        .content-card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .content-card ul {
            list-style: none;
            padding-left: 0;
        }

        .content-card ul li {
            padding: 0.5rem 0;
            color: #666;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .content-card ul li:before {
            content: "✓";
            color: #34A853;
            font-weight: bold;
            flex-shrink: 0;
        }

        /* Info Section */
        .info-section {
            padding: 6rem 2rem;
            background-color: #FFFFFF;
        }

        .info-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .info-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .info-header h2 {
            font-size: 2.5rem;
            font-weight: 900;
            color: #1A1A1A;
            margin-bottom: 2rem;
            letter-spacing: -1px;
        }

        .info-tabs {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }

        .tab-btn {
            padding: 0.75rem 1.5rem;
            border: 1px solid #E5E5E5;
            border-radius: 24px;
            background-color: #FFFFFF;
            color: #666;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .tab-btn.active {
            background-color: #1A1A1A;
            color: #FFFFFF;
            border-color: #1A1A1A;
        }

        .tab-btn:hover {
            border-color: #1A1A1A;
        }

        .info-content {
            display: none;
        }

        .info-content.active {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .info-image {
            border-radius: 16px;
            overflow: hidden;
        }

        .info-image img {
            width: 100%;
            height: auto;
            display: block;
        }

        .info-text h3 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 2rem;
            color: #1A1A1A;
        }

        .info-list {
            list-style: none;
        }

        .info-list li {
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .info-icon {
            width: 24px;
            height: 24px;
            background-color: #34A853;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.875rem;
            flex-shrink: 0;
            margin-top: 0.25rem;
        }

        .info-list-content h4 {
            font-size: 1.125rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #1A1A1A;
        }

        .info-list-content p {
            color: #666;
            font-size: 0.9375rem;
            line-height: 1.6;
        }

        /* FAQ Section */
        .faq-section {
            padding: 6rem 2rem;
            background-color: #F5F5F5;
        }

        .faq-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .faq-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .faq-header h2 {
            font-size: 2.5rem;
            font-weight: 900;
            color: #1A1A1A;
            margin-bottom: 1rem;
            letter-spacing: -1px;
        }

        .faq-header p {
            font-size: 1.125rem;
            color: #666;
        }

        .faq-item {
            background-color: #FFFFFF;
            margin-bottom: 1rem;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #E5E5E5;
        }

        .faq-question {
            width: 100%;
            padding: 1.75rem 2rem;
            background-color: #FFFFFF;
            border: none;
            text-align: left;
            font-size: 1.125rem;
            font-weight: 700;
            color: #1A1A1A;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }

        .faq-question:hover {
            color: #34A853;
        }

        .faq-icon {
            font-size: 1.5rem;
            font-weight: 300;
            transition: transform 0.3s;
        }

        .faq-item.active .faq-icon {
            transform: rotate(45deg);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .faq-answer-content {
            padding: 0 2rem 1.75rem 2rem;
            color: #666;
            font-size: 1rem;
            line-height: 1.7;
        }

        .faq-item.active .faq-answer {
            max-height: 500px;
        }

        /* Footer */
        .footer {
            background-color: #1A1A1A;
            color: #FFFFFF;
            padding: 3rem 2rem 2rem;
            text-align: center;
        }

        .footer p {
            color: #999;
            font-size: 0.875rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .support-hero h1 {
                font-size: 2.5rem;
            }

            .categories-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .info-content {
                grid-template-columns: 1fr;
            }

            .info-tabs {
                gap: 0.5rem;
            }

            .tab-btn {
                font-size: 0.75rem;
                padding: 0.625rem 1rem;
            }

            .faq-question {
                font-size: 1rem;
                padding: 1.5rem 1.5rem;
            }

            .faq-answer-content {
                padding: 0 1.5rem 1.5rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-content">
            <a href="{{ url('/') }}" class="logo" style="text-decoration: none;">Domini</a>
            <div class="nav-right">
                <a href="{{ url('/notre-menu') }}" class="nav-link">Notre cuisine</a>
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

    <!-- Hero Section -->
    <section class="support-hero">
        <h1>Besoin d'aide ?</h1>
        <div class="search-container">
            <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
            </svg>
            <input type="text" class="search-input" placeholder="Écrivez votre question">
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section">
        <div class="categories-container">
            <div class="categories-grid">
                <div class="category-card active" data-category="employes">
                    <h3>Pour les employés</h3>
                </div>
                <div class="category-card" data-category="entreprises">
                    <h3>Pour les entreprises</h3>
                </div>
                <div class="category-card" data-category="livreurs">
                    <h3>Pour les livreurs</h3>
                </div>
                <div class="category-card" data-category="business">
                    <h3>Domini Business</h3>
                </div>
                <div class="category-card" data-category="confidentialite">
                    <h3>Confidentialité</h3>
                </div>
                <div class="category-card" data-category="cuisine">
                    <h3>Notre cuisine</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Category Content Section -->
    <section class="category-content-section">
        <!-- Contenu Pour les employés -->
        <div class="category-content active" id="content-employes">
            <div class="content-header">
                <h2>Pour les employés</h2>
                <p>Tout ce que vous devez savoir pour profiter pleinement de Domini au quotidien</p>
            </div>
            <div class="content-cards">
                <div class="content-card">
                    <h3>Comment commander ?</h3>
                    <p>Commandez votre repas en toute simplicité :</p>
                    <ul>
                        <li>Téléchargez l'application Domini</li>
                        <li>Inscrivez-vous avec votre email professionnel</li>
                        <li>Parcourez le menu du jour</li>
                        <li>Validez votre commande avant 10h30</li>
                        <li>Récupérez entre 11h30 et 13h30</li>
                    </ul>
                </div>
                <div class="content-card">
                    <h3>Votre quota quotidien</h3>
                    <p>Votre entreprise vous offre :</p>
                    <ul>
                        <li>1 plat par jour inclus</li>
                        <li>Subvention totale ou partielle</li>
                        <li>Plats de 1 500 à 3 000 FCFA</li>
                        <li>Variété de choix chaque jour</li>
                    </ul>
                </div>
                <div class="content-card">
                    <h3>Récupération de votre repas</h3>
                    <p>Simple et sécurisé :</p>
                    <ul>
                        <li>Notification à la livraison</li>
                        <li>Utilisez votre carte NFC</li>
                        <li>Casier personnel sécurisé</li>
                        <li>Maintien de la température</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Contenu Pour les entreprises -->
        <div class="category-content" id="content-entreprises">
            <div class="content-header">
                <h2>Pour les entreprises</h2>
                <p>Installez Domini dans votre entreprise et améliorez la qualité de vie au travail</p>
            </div>
            <div class="content-cards">
                <div class="content-card">
                    <h3>Installation</h3>
                    <p>Un processus simple et rapide :</p>
                    <ul>
                        <li>Contactez notre équipe commerciale</li>
                        <li>Démonstration gratuite</li>
                        <li>Installation des casiers en 48h</li>
                        <li>Formation de vos équipes</li>
                        <li>Accompagnement continu</li>
                    </ul>
                </div>
                <div class="content-card">
                    <h3>Avantages</h3>
                    <p>Pour votre entreprise :</p>
                    <ul>
                        <li>Amélioration de la productivité</li>
                        <li>Réduction du temps de pause</li>
                        <li>Satisfaction des employés</li>
                        <li>Aucun investissement en cuisine</li>
                        <li>Gestion simplifiée</li>
                    </ul>
                </div>
                <div class="content-card">
                    <h3>Tarification flexible</h3>
                    <p>Adaptée à vos besoins :</p>
                    <ul>
                        <li>Subvention totale ou partielle</li>
                        <li>Gestion du budget employé</li>
                        <li>Facturation mensuelle</li>
                        <li>Reporting détaillé</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Contenu Pour les livreurs -->
        <div class="category-content" id="content-livreurs">
            <div class="content-header">
                <h2>Pour les livreurs</h2>
                <p>Rejoignez l'équipe Domini et livrez en toute simplicité</p>
            </div>
            <div class="content-cards">
                <div class="content-card">
                    <h3>Devenir livreur</h3>
                    <p>Les prérequis :</p>
                    <ul>
                        <li>Avoir un véhicule (moto/voiture)</li>
                        <li>Permis de conduire valide</li>
                        <li>Smartphone Android/iOS</li>
                        <li>Disponibilité 11h-14h</li>
                        <li>Casier judiciaire vierge</li>
                    </ul>
                </div>
                <div class="content-card">
                    <h3>Vos avantages</h3>
                    <p>Ce que Domini vous offre :</p>
                    <ul>
                        <li>Rémunération attractive</li>
                        <li>Paiement hebdomadaire</li>
                        <li>Assurance incluse</li>
                        <li>Équipement fourni</li>
                        <li>Flexibilité des horaires</li>
                    </ul>
                </div>
                <div class="content-card">
                    <h3>Le processus</h3>
                    <p>Comment ça marche :</p>
                    <ul>
                        <li>Inscription en ligne</li>
                        <li>Formation de 2 jours</li>
                        <li>Récupération dans nos cuisines</li>
                        <li>Livraison dans les casiers</li>
                        <li>Application de suivi</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Contenu Domini Business -->
        <div class="category-content" id="content-business">
            <div class="content-header">
                <h2>Domini Business</h2>
                <p>La solution complète pour gérer l'alimentation de vos équipes</p>
            </div>
            <div class="content-cards">
                <div class="content-card">
                    <h3>Dashboard entreprise</h3>
                    <p>Gérez tout en un clic :</p>
                    <ul>
                        <li>Tableau de bord en temps réel</li>
                        <li>Gestion des employés</li>
                        <li>Suivi des commandes</li>
                        <li>Statistiques détaillées</li>
                        <li>Export des données</li>
                    </ul>
                </div>
                <div class="content-card">
                    <h3>Personnalisation</h3>
                    <p>À votre image :</p>
                    <ul>
                        <li>Budget par employé</li>
                        <li>Taux de subvention flexible</li>
                        <li>Menus personnalisés</li>
                        <li>Restrictions alimentaires</li>
                        <li>Jours de service</li>
                    </ul>
                </div>
                <div class="content-card">
                    <h3>Support dédié</h3>
                    <p>Toujours à vos côtés :</p>
                    <ul>
                        <li>Account manager dédié</li>
                        <li>Support prioritaire</li>
                        <li>Formations régulières</li>
                        <li>Optimisation continue</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Contenu Confidentialité -->
        <div class="category-content" id="content-confidentialite">
            <div class="content-header">
                <h2>Confidentialité et sécurité</h2>
                <p>Vos données sont protégées et sécurisées</p>
            </div>
            <div class="content-cards">
                <div class="content-card">
                    <h3>Protection des données</h3>
                    <p>Nous respectons votre vie privée :</p>
                    <ul>
                        <li>Conformité RGPD</li>
                        <li>Données chiffrées</li>
                        <li>Hébergement sécurisé</li>
                        <li>Aucune revente de données</li>
                        <li>Droit à l'oubli respecté</li>
                    </ul>
                </div>
                <div class="content-card">
                    <h3>Sécurité des paiements</h3>
                    <p>Transactions 100% sécurisées :</p>
                    <ul>
                        <li>Paiement SSL crypté</li>
                        <li>Partenaires bancaires agréés</li>
                        <li>Aucune carte stockée</li>
                        <li>Authentification 3D Secure</li>
                    </ul>
                </div>
                <div class="content-card">
                    <h3>Vos droits</h3>
                    <p>Vous gardez le contrôle :</p>
                    <ul>
                        <li>Accès à vos données</li>
                        <li>Modification à tout moment</li>
                        <li>Suppression sur demande</li>
                        <li>Export de vos informations</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Contenu Notre cuisine -->
        <div class="category-content" id="content-cuisine">
            <div class="content-header">
                <h2>Notre cuisine</h2>
                <p>Des plats préparés avec soin par nos chefs</p>
            </div>
            <div class="content-cards">
                <div class="content-card">
                    <h3>Nos chefs</h3>
                    <p>Une équipe passionnée :</p>
                    <ul>
                        <li>Chefs diplômés et expérimentés</li>
                        <li>Cuisine traditionnelle et moderne</li>
                        <li>Recettes variées chaque jour</li>
                        <li>Respect des normes d'hygiène</li>
                        <li>Formation continue</li>
                    </ul>
                </div>
                <div class="content-card">
                    <h3>Nos ingrédients</h3>
                    <p>Qualité garantie :</p>
                    <ul>
                        <li>Produits frais locaux</li>
                        <li>Traçabilité totale</li>
                        <li>Contrôles qualité quotidiens</li>
                        <li>Fournisseurs certifiés</li>
                        <li>Sans conservateurs</li>
                    </ul>
                </div>
                <div class="content-card">
                    <h3>Notre menu</h3>
                    <p>Variété et équilibre :</p>
                    <ul>
                        <li>Menu renouvelé quotidiennement</li>
                        <li>Options végétariennes</li>
                        <li>Plats halal disponibles</li>
                        <li>Portions généreuses</li>
                        <li>Prix de 1 500 à 3 000 FCFA</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Info Section -->
    <section class="info-section">
        <div class="info-container">
            <div class="info-header">
                <h2>Rejoignez Domini</h2>
                <div class="info-tabs">
                    <button class="tab-btn active" data-tab="employe">Employé</button>
                    <button class="tab-btn" data-tab="livreur">Livreur</button>
                    <button class="tab-btn" data-tab="plats">Nos plats</button>
                    <button class="tab-btn" data-tab="entreprise">Entreprise</button>
                </div>
            </div>

            <!-- Contenu Employé -->
            <div class="info-content active" id="tab-employe">
                <div class="info-image">
                    <img src="{{ asset('slide/beautiful-young-woman-shopping-food.jpg') }}" alt="Employé Domini">
                </div>
                <div class="info-text">
                    <h3>Commandez facilement vos repas</h3>
                    <ul class="info-list">
                        <li>
                            <div class="info-icon">1</div>
                            <div class="info-list-content">
                                <h4>Commandez en quelques clics</h4>
                                <p>Accédez à notre menu varié de plats cuisinés par Domini. Commandez votre repas du midi en quelques secondes.</p>
                            </div>
                        </li>
                        <li>
                            <div class="info-icon">2</div>
                            <div class="info-list-content">
                                <h4>Récupérez quand vous voulez</h4>
                                <p>Votre repas vous attend dans votre casier sécurisé. Déverrouillez-le avec votre carte NFC et dégustez.</p>
                            </div>
                        </li>
                        <li>
                            <div class="info-icon">3</div>
                            <div class="info-list-content">
                                <h4>Profitez de la subvention entreprise</h4>
                                <p>Votre entreprise subventionne tout ou partie de votre repas quotidien. Plats de 1 500 à 3 000 FCFA, avec un plat par jour inclus.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Contenu Livreur -->
            <div class="info-content" id="tab-livreur">
                <div class="info-image">
                    <img src="{{ asset('slide/warehouse-technician-inspecting-cargo-details-shelves-large-scale-fulfillment-center.jpg') }}" alt="Livreur Domini">
                </div>
                <div class="info-text">
                    <h3>Générez des revenus avec Domini</h3>
                    <ul class="info-list">
                        <li>
                            <div class="info-icon">1</div>
                            <div class="info-list-content">
                                <h4>Livraison simple et rapide</h4>
                                <p>Récupérez les commandes dans nos cuisines et livrez-les directement dans les casiers des entreprises. Pas de contact client, pas de complications.</p>
                            </div>
                        </li>
                        <li>
                            <div class="info-icon">2</div>
                            <div class="info-list-content">
                                <h4>Horaires flexibles</h4>
                                <p>Travaillez principalement entre 11h et 14h. Profitez de votre temps libre le reste de la journée tout en générant un revenu stable.</p>
                            </div>
                        </li>
                        <li>
                            <div class="info-icon">3</div>
                            <div class="info-list-content">
                                <h4>Rémunération attractive</h4>
                                <p>Paiement hebdomadaire garanti, assurance incluse, équipement fourni. Plus vous livrez, plus vous gagnez.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Contenu Nos plats -->
            <div class="info-content" id="tab-plats">
                <div class="info-image">
                    <img src="{{ asset('menu/side-view-pilaf-with-stewed-beef-meat-plate.jpg') }}" alt="Plats Domini">
                </div>
                <div class="info-text">
                    <h3>Des plats préparés avec passion</h3>
                    <ul class="info-list">
                        <li>
                            <div class="info-icon">1</div>
                            <div class="info-list-content">
                                <h4>Chefs diplômés et expérimentés</h4>
                                <p>Notre équipe de chefs professionnels prépare quotidiennement des plats savoureux en respectant les normes d'hygiène les plus strictes.</p>
                            </div>
                        </li>
                        <li>
                            <div class="info-icon">2</div>
                            <div class="info-list-content">
                                <h4>Ingrédients frais et locaux</h4>
                                <p>Nous sélectionnons rigoureusement nos fournisseurs pour garantir la fraîcheur et la qualité de chaque ingrédient. Traçabilité totale de la source à l'assiette.</p>
                            </div>
                        </li>
                        <li>
                            <div class="info-icon">3</div>
                            <div class="info-list-content">
                                <h4>Menu varié et équilibré</h4>
                                <p>Menu renouvelé quotidiennement avec options végétariennes et halal. Portions généreuses de 1 500 à 3 000 FCFA.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Contenu Entreprise -->
            <div class="info-content" id="tab-entreprise">
                <div class="info-image">
                    <img src="{{ asset('slide/advisory-board-members-meeting-boardroom-establish-future-development-plan.jpg') }}" alt="Entreprise Domini">
                </div>
                <div class="info-text">
                    <h3>Améliorez la vie de vos employés</h3>
                    <ul class="info-list">
                        <li>
                            <div class="info-icon">1</div>
                            <div class="info-list-content">
                                <h4>Installation rapide et simple</h4>
                                <p>Nos casiers intelligents sont installés en 48h dans vos locaux. Démonstration gratuite et formation complète de vos équipes incluses.</p>
                            </div>
                        </li>
                        <li>
                            <div class="info-icon">2</div>
                            <div class="info-list-content">
                                <h4>Productivité augmentée</h4>
                                <p>Vos employés ne perdent plus de temps pour déjeuner. Réduction du temps de pause et satisfaction accrue de vos équipes.</p>
                            </div>
                        </li>
                        <li>
                            <div class="info-icon">3</div>
                            <div class="info-list-content">
                                <h4>Gestion simplifiée</h4>
                                <p>Dashboard en temps réel, gestion du budget par employé, facturation mensuelle. Subventionnez totalement ou partiellement selon votre politique.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="faq-container">
            <div class="faq-header">
                <h2>Questions fréquentes</h2>
                <p>Trouvez rapidement des réponses à vos questions</p>
            </div>

            <div class="faq-list">
                <div class="faq-item">
                    <button class="faq-question">
                        Comment fonctionne Domini ?
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            Domini est une plateforme de livraison de repas pour entreprises. Votre employeur installe des casiers connectés dans vos locaux. Vous commandez via l'application, le livreur dépose votre repas dans votre casier personnel, et vous le récupérez quand vous voulez avec votre carte NFC.
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        Comment récupérer ma commande ?
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            Une fois votre commande livrée, vous recevez une notification. Rendez-vous au casier Domini de votre entreprise, approchez votre carte NFC du lecteur, et votre casier s'ouvre automatiquement. C'est simple, rapide et sécurisé !
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        Quels sont les horaires de livraison ?
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            Les livraisons sont effectuées tous les jours du lundi au vendredi entre 11h30 et 13h30. Vous pouvez commander jusqu'à 10h30 le jour même pour une livraison à midi.
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        Que faire si j'ai perdu ma carte NFC ?
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            Contactez immédiatement le service client via l'application pour désactiver votre carte perdue. Vous pouvez commander une nouvelle carte gratuitement, et temporairement utiliser un code QR depuis l'application pour ouvrir votre casier.
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        Puis-je annuler ou modifier ma commande ?
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            Oui, vous pouvez annuler ou modifier votre commande jusqu'à 30 minutes après l'avoir passée, à condition qu'elle n'ait pas encore été préparée. Rendez-vous dans "Mes commandes" dans l'application.
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        Combien coûtent les plats ?
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            Nos plats sont proposés entre 1 500 et 3 000 FCFA. Chaque employé a droit à un plat par jour, subventionné en totalité ou en partie par son entreprise selon la politique définie. Le montant restant à votre charge dépend du taux de subvention choisi par votre employeur.
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        Qui prépare les plats ?
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            Tous nos plats sont préparés quotidiennement par les chefs cuisiniers de Domini dans nos cuisines centrales. Nous garantissons la fraîcheur, la qualité et la traçabilité de tous nos ingrédients pour vous offrir une expérience culinaire exceptionnelle.
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        Mon entreprise peut-elle installer Domini ?
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            Absolument ! Domini est disponible pour toutes les entreprises de plus de 50 employés. Contactez notre équipe commerciale pour une démonstration gratuite et un devis personnalisé. L'installation est rapide et sans engagement.
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        Les repas sont-ils conservés au chaud ?
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            Oui, nos casiers intelligents sont équipés d'un système de maintien de température. Vos plats chauds restent chauds et vos plats froids restent frais jusqu'à ce que vous les récupériez.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 Domini. Tous droits réservés.</p>
    </footer>

    <script>
        // Navigation scroll effect
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('nav');
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });

        // FAQ Accordion
        document.querySelectorAll('.faq-question').forEach(button => {
            button.addEventListener('click', () => {
                const faqItem = button.parentElement;
                const isActive = faqItem.classList.contains('active');
                
                // Close all items
                document.querySelectorAll('.faq-item').forEach(item => {
                    item.classList.remove('active');
                });
                
                // Open clicked item if it wasn't active
                if (!isActive) {
                    faqItem.classList.add('active');
                }
            });
        });

        // Search functionality
        const searchInput = document.querySelector('.search-input');
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.faq-item').forEach(item => {
                const question = item.querySelector('.faq-question').textContent.toLowerCase();
                const answer = item.querySelector('.faq-answer-content').textContent.toLowerCase();
                
                if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = searchTerm ? 'none' : 'block';
                }
            });
        });

        // Category switching
        document.querySelectorAll('.category-card').forEach(card => {
            card.addEventListener('click', () => {
                const category = card.getAttribute('data-category');
                
                // Remove active class from all cards
                document.querySelectorAll('.category-card').forEach(c => {
                    c.classList.remove('active');
                });
                
                // Add active class to clicked card
                card.classList.add('active');
                
                // Hide all content sections
                document.querySelectorAll('.category-content').forEach(content => {
                    content.classList.remove('active');
                });
                
                // Show selected content
                const selectedContent = document.getElementById('content-' + category);
                if (selectedContent) {
                    selectedContent.classList.add('active');
                    // Smooth scroll to content
                    selectedContent.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Tab switching for Info Section
        document.querySelectorAll('.tab-btn').forEach(button => {
            button.addEventListener('click', () => {
                const tab = button.getAttribute('data-tab');
                
                // Remove active class from all buttons
                document.querySelectorAll('.tab-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                
                // Add active class to clicked button
                button.classList.add('active');
                
                // Hide all tab contents
                document.querySelectorAll('.info-content').forEach(content => {
                    content.classList.remove('active');
                });
                
                // Show selected tab content
                const selectedTab = document.getElementById('tab-' + tab);
                if (selectedTab) {
                    selectedTab.classList.add('active');
                }
            });
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
