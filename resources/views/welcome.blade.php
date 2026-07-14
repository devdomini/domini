<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Domini - Livraison de Repas en Entreprise</title>

        <!-- Fonts : variable font (1 fichier) + chargement non bloquant -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400..900&display=swap" onload="this.onload=null;this.rel='stylesheet'">
        <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400..900&display=swap"></noscript>

        {{-- Image LCP probable : première grande photo sous le hero --}}
        <link rel="preload" as="image" href="{{ asset('diverse-team-chefs-making-meal-preparations-with-ingredients-cooking-delicious-gourmet-dish-restaurant-kitchen-people-uniform-working-as-cooks-preparing-gastronomy-food-recipe.jpg') }}">

        <style>
            :root {
                --primary: #FF0000;
                --primary-dark: #CC0000;
                --secondary: #000000;
                --dark: #000000;
                --dark-light: #1A1A1A;
                --gray: #5A5A5A;
                --light: #FFFFFF;
                --white: #FFFFFF;
                --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.06);
                --shadow-md: 0 8px 24px rgba(0, 0, 0, 0.08);
                --shadow-lg: 0 16px 48px rgba(0, 0, 0, 0.12);
                --shadow-primary: 0 8px 32px rgba(255, 0, 0, 0.25);
                --transition-fast: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                --transition-normal: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                --transition-slow: 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                background-color: var(--light);
                color: var(--dark);
                line-height: 1.6;
                overflow-x: hidden;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }

            /* Navigation améliorée avec glassmorphism */
            nav {
                position: fixed;
                top: 0;
                width: 100%;
                background-color: rgba(245, 245, 245, 0.85);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border-bottom: 1px solid rgba(229, 229, 229, 0.5);
                z-index: 1000;
                height: 80px;
                transition: var(--transition-normal);
            }

            nav.scrolled {
                background-color: rgba(255, 255, 255, 0.95);
                box-shadow: var(--shadow-md);
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
                color: var(--dark);
                letter-spacing: -0.5px;
                transition: var(--transition-fast);
            }

            .logo:hover {
                color: var(--primary);
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
                color: var(--dark);
                font-weight: 500;
                cursor: pointer;
                padding: 0.5rem 0.75rem;
                text-decoration: none;
                transition: var(--transition-normal);
                border-radius: 10px;
                background-color: transparent;
            }

            .language-selector:hover {
                background-color: rgba(255, 0, 0, 0.08);
                color: var(--primary);
            }

            .flag-icon {
                font-size: 1.25rem;
            }

            .nav-link {
                text-decoration: none;
                color: var(--dark);
                font-weight: 500;
                font-size: 0.95rem;
                transition: var(--transition-normal);
                padding: 0.5rem 0;
                position: relative;
            }

            .nav-link::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                width: 0;
                height: 2px;
                background: linear-gradient(90deg, var(--primary), var(--secondary));
                transition: var(--transition-normal);
                border-radius: 2px;
            }

            .nav-link:hover {
                color: var(--primary);
            }

            .nav-link:hover::after {
                width: 100%;
            }

            .btn-signup {
                background: linear-gradient(135deg, var(--dark-light) 0%, var(--dark) 100%);
                color: var(--white);
                padding: 0.75rem 1.5rem;
                border-radius: 12px;
                text-decoration: none;
                font-weight: 600;
                font-size: 0.95rem;
                transition: var(--transition-normal);
                border: none;
                cursor: pointer;
                display: inline-block;
                position: relative;
                overflow: hidden;
            }

            .btn-signup:hover {
                background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
                transform: translateY(-2px);
                box-shadow: var(--shadow-primary);
            }

            .menu-icon {
                display: flex;
                flex-direction: column;
                gap: 5px;
                cursor: pointer;
                padding: 0.6rem;
                border-radius: 10px;
                transition: var(--transition-normal);
                background-color: transparent;
            }

            .menu-icon:hover {
                background-color: rgba(255, 0, 0, 0.08);
            }

            .menu-icon span {
                width: 22px;
                height: 2px;
                background-color: var(--dark);
                display: block;
                transition: var(--transition-normal);
                border-radius: 2px;
            }

            .menu-icon:hover span {
                background-color: var(--primary);
            }

            .menu-icon:hover span:nth-child(1) {
                transform: translateX(3px);
            }

            .menu-icon:hover span:nth-child(3) {
                transform: translateX(-3px);
            }

            /* Hero Section améliorée */
            .hero {
                min-height: 80vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 100px 2rem 60px;
                background: linear-gradient(180deg, var(--light) 0%, rgba(255,255,255,0.8) 100%);
                position: relative;
                overflow: hidden;
            }

            /* Décoration de fond subtile */
            .hero::before {
                content: '';
                position: absolute;
                top: -50%;
                right: -20%;
                width: 80%;
                height: 150%;
                background: radial-gradient(ellipse at center, rgba(255, 0, 0, 0.03) 0%, transparent 70%);
                pointer-events: none;
            }

            /* Icônes de nourriture en arrière-plan */
            .food-icons-background {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
                z-index: 0;
            }

            .food-icon {
                position: absolute;
                opacity: 0.06;
                animation: float 20s infinite ease-in-out;
                filter: blur(0.5px);
            }

            .food-icon svg {
                color: var(--primary);
                filter: drop-shadow(0 4px 8px rgba(255, 0, 0, 0.1));
            }

            @keyframes float {
                0%, 100% {
                    transform: translateY(0) rotate(0deg) scale(1);
                }
                25% {
                    transform: translateY(-15px) rotate(3deg) scale(1.02);
                }
                50% {
                    transform: translateY(-30px) rotate(-3deg) scale(1);
                }
                75% {
                    transform: translateY(-15px) rotate(2deg) scale(0.98);
                }
            }

            /* Animation plus lente pour certaines icônes */
            .food-icon:nth-child(2n) {
                animation-duration: 25s;
            }

            .food-icon:nth-child(3n) {
                animation-duration: 30s;
                animation-direction: reverse;
            }

            .hero-content {
                max-width: 900px;
                margin: 0 auto;
                text-align: center;
                position: relative;
                z-index: 1;
            }

            .hero-title {
                font-size: 3.5rem;
                font-weight: 800;
                line-height: 1.15;
                color: var(--dark);
                margin-bottom: 1.75rem;
                letter-spacing: -1.5px;
                background: linear-gradient(135deg, var(--dark) 0%, var(--dark-light) 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .hero-subtitle {
                font-size: 1.2rem;
                color: var(--gray);
                margin-bottom: 2.5rem;
                line-height: 1.7;
                max-width: 680px;
                margin-left: auto;
                margin-right: auto;
                font-weight: 500;
            }

            .hero-buttons {
                display: flex;
                gap: 1.25rem;
                justify-content: center;
                flex-wrap: wrap;
            }

            .btn-primary {
                background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
                color: var(--white);
                padding: 1rem 2.25rem;
                border-radius: 14px;
                text-decoration: none;
                font-weight: 600;
                font-size: 1.05rem;
                transition: var(--transition-normal);
                border: none;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                box-shadow: 0 4px 16px rgba(255, 0, 0, 0.25);
                position: relative;
                overflow: hidden;
            }

            .btn-primary::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                transition: var(--transition-slow);
            }

            .btn-primary:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 28px rgba(255, 0, 0, 0.35);
            }

            .btn-primary:hover::before {
                left: 100%;
            }

            .btn-secondary {
                background-color: var(--white);
                color: var(--dark);
                padding: 1rem 2.25rem;
                border-radius: 14px;
                text-decoration: none;
                font-weight: 600;
                font-size: 1.05rem;
                transition: var(--transition-normal);
                border: 2px solid rgba(26, 26, 26, 0.1);
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                box-shadow: var(--shadow-sm);
            }

            .btn-secondary:hover {
                background-color: var(--dark);
                color: var(--white);
                border-color: var(--dark);
                transform: translateY(-3px);
                box-shadow: var(--shadow-md);
            }

            /* Image Section - Full Width améliorée */
            .image-section {
                width: 100%;
                padding: 3rem;
                background-color: var(--light);
                position: relative;
            }

            .image-container {
                max-width: 1400px;
                margin: 0 auto;
                position: relative;
                border-radius: 24px;
                overflow: hidden;
                box-shadow: var(--shadow-lg);
            }

            .image-container::before {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(180deg, transparent 60%, rgba(0,0,0,0.1) 100%);
                z-index: 1;
                pointer-events: none;
            }

            .featured-image {
                width: 100%;
                height: auto;
                max-height: 70vh;
                object-fit: cover;
                object-position: center;
                display: block;
                transition: var(--transition-slow);
            }

            .image-container:hover .featured-image {
                transform: scale(1.02);
            }

            /* Responsive */
            @media (max-width: 768px) {
                .nav-content {
                    padding: 1rem 1.5rem;
                }

                .nav-right > *:not(.menu-icon):not(.btn-signup) {
                    display: none;
                }

                .hero {
                    padding: 120px 1.5rem 60px;
                    min-height: 70vh;
                }

                .hero-title {
                    font-size: 2.25rem;
                    letter-spacing: -0.5px;
                }

                .hero-subtitle {
                    font-size: 1rem;
                }

                .hero-buttons {
                    flex-direction: column;
                    gap: 1rem;
                }

                .btn-primary,
                .btn-secondary {
                    width: 100%;
                    justify-content: center;
                }

                .services-section,
                .how-it-works-section {
                    padding: 4rem 1.5rem;
                }

                .services-title,
                .how-it-works-title {
                    font-size: 2rem;
                }

                .impact-section {
                    min-height: auto;
                    padding: 4rem 0;
                }

                .impact-container {
                    grid-template-columns: 1fr;
                    padding: 2rem 1.5rem 4rem;
                    gap: 2rem;
                }

                .impact-image-wrapper {
                    order: -1;
                }

                .impact-image {
                    max-height: 300px;
                }

                .impact-title {
                    font-size: 2rem;
                }

                .impact-controls {
                    position: relative;
                    bottom: 0;
                    padding: 2rem 1.5rem 0;
                }

                .impact-nav-btn-left,
                .impact-nav-btn-right {
                    display: none;
                }

                .menu-content-area {
                    padding: 4rem 1.5rem;
                }

                .menu-main-title {
                    font-size: 2.25rem;
                }
            }

            @media (min-width: 769px) {
                .menu-icon {
                    display: none;
                }
            }

            /* ========================================
               ANIMATIONS & TRANSITIONS MODERNES
               ======================================== */

            /* Animation d'apparition au scroll */
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

            @keyframes fadeInLeft {
                from {
                    opacity: 0;
                    transform: translateX(-40px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            @keyframes fadeInRight {
                from {
                    opacity: 0;
                    transform: translateX(40px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            @keyframes scaleIn {
                from {
                    opacity: 0;
                    transform: scale(0.9);
                }
                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }

            /* Classes pour déclencher les animations */
            .animate-on-scroll {
                opacity: 0;
            }

            .animate-on-scroll.animated {
                animation-duration: 0.8s;
                animation-fill-mode: forwards;
                animation-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            }

            .fade-in-up {
                animation-name: fadeInUp;
            }

            .fade-in-left {
                animation-name: fadeInLeft;
            }

            .fade-in-right {
                animation-name: fadeInRight;
            }

            .scale-in {
                animation-name: scaleIn;
            }

            /* Délais progressifs pour effet cascade */
            .delay-100 { animation-delay: 0.1s; }
            .delay-200 { animation-delay: 0.2s; }
            .delay-300 { animation-delay: 0.3s; }
            .delay-400 { animation-delay: 0.4s; }
            .delay-500 { animation-delay: 0.5s; }
            .delay-600 { animation-delay: 0.6s; }

            /* Amélioration des transitions sur hover */
            .service-card,
            .step-row,
            .menu-grid-item,
            .featured-image,
            .slider-btn,
            .impact-nav-btn-left,
            .impact-nav-btn-right {
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                           opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                           box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            /* Effet de brillance sur les boutons */
            @keyframes shine {
                0% {
                    background-position: -200% center;
                }
                100% {
                    background-position: 200% center;
                }
            }

            .btn-primary, .btn-secondary, .btn-signup {
                position: relative;
                overflow: hidden;
                background-size: 200% auto;
            }

            .btn-primary::before,
            .btn-secondary::before,
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

            .btn-primary:hover::before,
            .btn-secondary:hover::before,
            .btn-signup:hover::before {
                left: 100%;
            }

            .btn-primary:hover {
                box-shadow: 0 12px 35px rgba(255, 0, 0, 0.5);
            }

            .btn-secondary:hover {
                box-shadow: 0 12px 35px rgba(44, 44, 44, 0.3);
            }

            /* Animation des cartes de service */
            .service-card {
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .service-card:hover {
                transform: translateY(-10px) scale(1.03);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            }

            .service-card-overlay {
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .service-card:hover .service-card-overlay {
                background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.6));
            }

            /* Animation des images au hover */
            .service-card-bg,
            .featured-image,
            .step-image img {
                transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .service-card:hover .service-card-bg,
            .image-container:hover .featured-image,
            .step-image:hover img {
                transform: scale(1.05);
            }

            /* Effet de parallaxe sur hero */
            @keyframes parallaxFloat {
                0%, 100% {
                    transform: translateY(0);
                }
                50% {
                    transform: translateY(-10px);
                }
            }

            .hero-content {
                animation: parallaxFloat 3s ease-in-out infinite;
            }

            /* Pulsation sur les dots de navigation */
            @keyframes pulse {
                0%, 100% {
                    opacity: 1;
                    transform: scale(1);
                }
                50% {
                    opacity: 0.7;
                    transform: scale(1.1);
                }
            }

            .impact-dot.active {
                animation: pulse 2s ease-in-out infinite;
            }

            /* Effet de survol sur les étapes */
            .step-row {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .step-row:hover {
                transform: translateX(10px);
            }

            .step-row.reverse:hover {
                transform: translateX(-10px);
            }

            /* Animation du slider */
            .services-slider {
                scroll-behavior: smooth;
            }

            /* Effet de glissement sur les contrôles */
            .slider-btn,
            .impact-nav-btn-left,
            .impact-nav-btn-right {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .slider-btn:hover {
                transform: scale(1.15);
                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            }

            .impact-nav-btn-left:hover,
            .impact-nav-btn-right:hover {
                transform: scale(1.15);
                background-color: rgba(255, 255, 255, 0.1);
            }

            /* Animation du menu grid */
            .menu-grid-item {
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .menu-grid-item:hover {
                transform: scale(1.05);
                z-index: 10;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            }

            /* Effet de texte qui apparaît */
            @keyframes textReveal {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                    filter: blur(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                    filter: blur(0);
                }
            }

            .hero-title,
            .hero-subtitle {
                animation: textReveal 1s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            }

            .hero-subtitle {
                animation-delay: 0.3s;
            }

            .hero-buttons {
                animation: textReveal 1s cubic-bezier(0.4, 0, 0.2, 1) 0.6s forwards;
                opacity: 0;
            }

            /* Effet de gradient animé sur l'impact section */
            .impact-section {
                background: linear-gradient(135deg, #FF0000 0%, #CC0000 100%);
                background-size: 200% 200%;
                animation: gradientShift 15s ease infinite;
            }

            @keyframes gradientShift {
                0%, 100% {
                    background-position: 0% 50%;
                }
                50% {
                    background-position: 100% 50%;
                }
            }

            /* Amélioration des liens avec underline animé */
            .nav-link {
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

            .nav-link:hover::after {
                width: 100%;
            }

            /* Animation smooth sur la navigation */
            nav {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            nav.scrolled {
                box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
                backdrop-filter: blur(10px);
                background-color: rgba(245, 245, 245, 0.95);
            }

            /* Effet de loading shimmer */
            @keyframes shimmer {
                0% {
                    background-position: -1000px 0;
                }
                100% {
                    background-position: 1000px 0;
                }
            }

            .featured-image {
                position: relative;
                overflow: hidden;
            }

            /* Responsive: désactiver certaines animations sur mobile */
            @media (prefers-reduced-motion: reduce) {
                *,
                *::before,
                *::after {
                    animation-duration: 0.01ms !important;
                    animation-iteration-count: 1 !important;
                    transition-duration: 0.01ms !important;
                }
            }

            /* Image Section */
            .image-section {
                padding: 4rem 3rem;
                background-color: #F5F5F5;
            }

            .image-container {
                max-width: 1300px;
                margin: 0 auto;
            }

            .featured-image {
                width: 100%;
                height: auto;
                border-radius: 24px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
                display: block;
            }

            @media (max-width: 768px) {
                .image-section {
                    padding: 2rem 1.5rem;
                }

                .featured-image {
                    border-radius: 16px;
                }
            }

            /* Services Section améliorée */
            .services-section {
                padding: 7rem 0;
                background: linear-gradient(180deg, var(--white) 0%, var(--light) 100%);
                overflow: hidden;
            }

            .services-header {
                text-align: center;
                margin-bottom: 4rem;
                padding: 0 3rem;
            }

            .services-title {
                font-size: 2.75rem;
                font-weight: 800;
                color: var(--dark);
                margin-bottom: 1.25rem;
                letter-spacing: -1px;
            }

            .services-subtitle {
                font-size: 1.1rem;
                color: var(--gray);
                max-width: 600px;
                margin: 0 auto;
                line-height: 1.7;
            }

            .services-slider-container {
                position: relative;
            }

            .services-slider {
                display: flex;
                gap: 1.75rem;
                overflow-x: auto;
                scroll-behavior: smooth;
                padding-left: 80px;
                padding-right: 3rem;
                padding-bottom: 1rem;
                scrollbar-width: none;
                -ms-overflow-style: none;
            }

            .services-slider::-webkit-scrollbar {
                display: none;
            }

            .service-card {
                position: relative;
                border-radius: 20px;
                overflow: hidden;
                min-width: 340px;
                width: 340px;
                height: 460px;
                cursor: pointer;
                transition: var(--transition-normal);
                flex-shrink: 0;
                box-shadow: var(--shadow-md);
            }

            .service-card:hover {
                transform: translateY(-8px) scale(1.02);
                box-shadow: var(--shadow-lg);
            }

            .service-card-bg {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                background-color: var(--dark-light);
                transition: var(--transition-slow);
            }

            .service-card:hover .service-card-bg {
                transform: scale(1.08);
            }

            .service-card-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(180deg, rgba(0,0,0,0.15) 0%, rgba(0,0,0,0.75) 100%);
                padding: 2rem;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                color: white;
                transition: var(--transition-normal);
            }

            .service-card:hover .service-card-overlay {
                background: linear-gradient(180deg, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.85) 100%);
            }

            .service-card-content h3 {
                font-size: 1.75rem;
                font-weight: 700;
                margin-bottom: 0.75rem;
                letter-spacing: -0.5px;
                text-shadow: 0 2px 8px rgba(0,0,0,0.3);
            }

            .service-card-content p {
                font-size: 0.95rem;
                line-height: 1.6;
                opacity: 0.9;
            }

            .service-card-btn {
                background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
                color: white;
                padding: 0.875rem 1.75rem;
                border-radius: 12px;
                text-decoration: none;
                font-weight: 600;
                display: inline-block;
                transition: var(--transition-normal);
                align-self: flex-start;
                font-size: 0.9rem;
                box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
            }

            .service-card-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(255, 0, 0, 0.4);
            }

            .service-card-btn.white {
                background: var(--white);
                color: var(--dark);
                box-shadow: var(--shadow-sm);
            }

            .service-card-btn.white:hover {
                background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
                color: white;
                box-shadow: 0 8px 20px rgba(255, 0, 0, 0.4);
            }

            .service-card.dark .service-card-overlay {
                background: linear-gradient(180deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.9) 100%);
            }

            .slider-controls {
                display: flex;
                gap: 0.75rem;
                justify-content: flex-end;
                padding: 2.5rem 3rem 0;
            }

            .slider-btn {
                width: 52px;
                height: 52px;
                border-radius: 50%;
                background-color: var(--white);
                border: 1px solid rgba(0,0,0,0.08);
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: var(--transition-normal);
                box-shadow: var(--shadow-sm);
            }

            .slider-btn:hover {
                background-color: var(--primary);
                border-color: var(--primary);
                transform: scale(1.08);
                box-shadow: var(--shadow-primary);
            }

            .slider-btn:hover svg {
                stroke: white;
            }

            .slider-btn svg {
                width: 20px;
                height: 20px;
                transition: var(--transition-fast);
            }

            @media (max-width: 768px) {
                .services-section {
                    padding: 3rem 0;
                }

                .services-header {
                    padding: 0 1.5rem;
                    margin-bottom: 2rem;
                }

                .services-title {
                    font-size: 2rem;
                }

                .services-slider {
                    padding-left: 1.5rem;
                    padding-right: 1.5rem;
                }

                .slider-controls {
                    padding: 1.5rem 1.5rem 0;
                }

                .service-card {
                    min-width: 280px;
                    width: 280px;
                    height: 420px;
                }

                .slider-controls {
                    padding: 1.5rem 1.5rem 0;
                }
            }

            /* Impact Section améliorée */
            .impact-section {
                background: linear-gradient(135deg, var(--primary) 0%, #b83a1c 50%, var(--primary-dark) 100%);
                color: white;
                padding: 0;
                position: relative;
                min-height: 70vh;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
            }

            .impact-section::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
                pointer-events: none;
            }

            .impact-container {
                width: 100%;
                max-width: 1400px;
                margin: 0 auto;
                padding: 5rem 80px;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 4rem;
                align-items: center;
                position: relative;
                z-index: 1;
            }

            .impact-slider-wrapper {
                display: flex;
                flex-direction: column;
            }

            .impact-slider {
                position: relative;
                padding: 2rem 0;
            }

            .impact-image-wrapper {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .impact-image {
                max-width: 100%;
                height: auto;
                max-height: 500px;
                object-fit: contain;
                filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));
                animation: floatImage 4s ease-in-out infinite;
            }

            @keyframes floatImage {
                0%, 100% {
                    transform: translateY(0);
                }
                50% {
                    transform: translateY(-15px);
                }
            }

            .impact-slide {
                display: none;
                opacity: 0;
                transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
                transform: translateY(20px);
            }

            .impact-slide.active {
                display: block;
                opacity: 1;
                transform: translateY(0);
            }

            .impact-content {
                max-width: 750px;
            }

            .impact-title {
                font-size: 3.25rem;
                font-weight: 800;
                line-height: 1.15;
                margin-bottom: 1.75rem;
                letter-spacing: -1.5px;
                color: var(--white);
                text-shadow: 0 2px 20px rgba(0,0,0,0.15);
            }

            .impact-subtitle {
                font-size: 1.1rem;
                color: rgba(255, 255, 255, 0.9);
                line-height: 1.75;
                margin-bottom: 3rem;
                font-weight: 400;
            }

            .impact-feature {
                margin-top: 3rem;
                padding: 2rem;
                background: rgba(255,255,255,0.08);
                border-radius: 16px;
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255,255,255,0.1);
            }

            .impact-feature h3 {
                font-size: 1.35rem;
                font-weight: 700;
                margin-bottom: 1rem;
                color: var(--white);
            }

            .impact-feature p {
                font-size: 1.05rem;
                color: rgba(255, 255, 255, 0.85);
                line-height: 1.7;
                margin-bottom: 1.5rem;
                max-width: 600px;
            }

            .impact-link {
                color: var(--primary);
                text-decoration: none;
                font-weight: 600;
                font-size: 1rem;
                transition: var(--transition-normal);
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.5rem 0;
            }

            .impact-link::after {
                content: '→';
                transition: var(--transition-fast);
            }

            .impact-link:hover {
                color: var(--white);
            }

            .impact-link:hover::after {
                transform: translateX(5px);
            }

            .impact-controls {
                position: relative;
                display: flex;
                justify-content: flex-start;
                align-items: center;
                padding: 1.5rem 0 0;
            }

            .impact-nav-btn-left,
            .impact-nav-btn-right {
                position: absolute;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                background-color: rgba(255,255,255,0.1);
                border: 1px solid rgba(255,255,255,0.2);
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: var(--transition-normal);
                color: white;
                backdrop-filter: blur(8px);
            }

            .impact-nav-btn-left {
                left: 80px;
            }

            .impact-nav-btn-right {
                right: 80px;
            }

            .impact-nav-btn-left:hover,
            .impact-nav-btn-right:hover {
                border-color: var(--white);
                background-color: rgba(255, 255, 255, 0.2);
                transform: scale(1.08);
            }

            .impact-dots {
                display: flex;
                gap: 0.6rem;
                justify-content: center;
            }

            .impact-dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background-color: rgba(255, 0, 0, 0.4);
                cursor: pointer;
                transition: var(--transition-normal);
            }

            .impact-dot:hover {
                background-color: rgba(255, 0, 0, 0.7);
            }

            .impact-dot.active {
                background-color: var(--primary);
                width: 32px;
                border-radius: 5px;
                box-shadow: 0 0 12px rgba(255, 0, 0, 0.5);
            }

            .impact-nav-btn-left svg,
            .impact-nav-btn-right svg {
                width: 20px;
                height: 20px;
            }

            @media (max-width: 768px) {
                .impact-section {
                    height: auto;
                    min-height: auto;
                    padding: 3rem 0;
                }

                .impact-container {
                    grid-template-columns: 1fr;
                    padding: 0 1.5rem 2rem;
                    gap: 2rem;
                }

                .impact-image-wrapper {
                    order: -1;
                }

                .impact-image {
                    max-height: 280px;
                }

                .impact-title {
                    font-size: 1.85rem;
                    letter-spacing: -1px;
                    margin-bottom: 1rem;
                }

                .impact-subtitle {
                    font-size: 1rem;
                    margin-bottom: 1.5rem;
                }

                .impact-feature {
                    margin-top: 1.5rem;
                    padding: 1.5rem;
                }

                .impact-feature h3 {
                    font-size: 1.15rem;
                }

                .impact-feature p {
                    font-size: 0.95rem;
                }

                .impact-controls {
                    position: relative;
                    bottom: 0;
                    padding: 1.5rem 0 0;
                }

                .impact-nav-btn-left,
                .impact-nav-btn-right {
                    display: none;
                }
            }

            /* Menu Section améliorée */
            .menu-showcase-section {
                background-color: var(--dark);
                padding: 0;
                position: relative;
            }

            .menu-images-grid {
                display: grid;
                grid-template-columns: repeat(8, 1fr);
                grid-template-rows: repeat(2, 220px);
                gap: 4px;
            }

            .menu-grid-item {
                overflow: hidden;
                position: relative;
            }

            .menu-grid-item::after {
                content: '';
                position: absolute;
                inset: 0;
                background: rgba(0,0,0,0.2);
                opacity: 1;
                transition: var(--transition-normal);
            }

            .menu-grid-item:hover::after {
                opacity: 0;
            }

            .menu-grid-item img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: var(--transition-slow);
            }

            .menu-grid-item:hover img {
                transform: scale(1.1);
            }

            .menu-grid-item:nth-child(1) {
                grid-column: span 2;
            }

            .menu-grid-item:nth-child(2) {
                grid-column: span 3;
            }

            .menu-grid-item:nth-child(3) {
                grid-column: span 3;
            }

            .menu-grid-item:nth-child(4) {
                grid-column: span 2;
            }

            .menu-grid-item:nth-child(5) {
                grid-column: span 4;
            }

            .menu-grid-item:nth-child(6) {
                grid-column: span 2;
            }

            .menu-content-area {
                padding: 6rem 80px;
                color: white;
                background: linear-gradient(180deg, var(--dark) 0%, #0a0a0a 100%);
            }

            .menu-content-container {
                max-width: 1000px;
                margin: 0 auto;
                text-align: center;
            }

            .menu-label {
                font-size: 0.9rem;
                font-weight: 600;
                margin-bottom: 1.5rem;
                color: var(--primary);
                text-transform: uppercase;
                letter-spacing: 3px;
            }

            .menu-main-title {
                font-size: 3.5rem;
                font-weight: 800;
                line-height: 1.15;
                margin-bottom: 1.75rem;
                letter-spacing: -1.5px;
                max-width: 800px;
                margin-left: auto;
                margin-right: auto;
            }

            .menu-description {
                font-size: 1.15rem;
                line-height: 1.8;
                margin-bottom: 2.5rem;
                max-width: 700px;
                margin-left: auto;
                margin-right: auto;
                opacity: 0.85;
            }

            .menu-btn {
                background-color: var(--white);
                color: var(--dark);
                padding: 1rem 2.5rem;
                border-radius: 14px;
                text-decoration: none;
                font-weight: 600;
                font-size: 1.05rem;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                transition: var(--transition-normal);
                box-shadow: var(--shadow-md);
            }

            .menu-btn:hover {
                background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
                color: var(--white);
                transform: translateY(-3px);
                box-shadow: 0 12px 32px rgba(255, 0, 0, 0.35);
            }

            @media (max-width: 768px) {
                .menu-images-grid {
                    grid-template-columns: repeat(4, 1fr);
                    grid-template-rows: repeat(2, 150px);
                }

                .menu-grid-item:nth-child(1),
                .menu-grid-item:nth-child(4) {
                    grid-column: span 1;
                }

                .menu-grid-item:nth-child(2),
                .menu-grid-item:nth-child(3) {
                    grid-column: span 2;
                }

                .menu-grid-item:nth-child(5) {
                    grid-column: span 2;
                }

                .menu-grid-item:nth-child(6) {
                    grid-column: span 2;
                }

                .menu-content-area {
                    padding: 4rem 1.5rem;
                }

                .menu-main-title {
                    font-size: 2.5rem;
                }

                .menu-description {
                    font-size: 1.125rem;
                }
            }

            /* How It Works Section améliorée */
            .how-it-works-section {
                background: linear-gradient(180deg, var(--light) 0%, var(--white) 50%, var(--light) 100%);
                padding: 7rem 80px;
            }

            .how-it-works-container {
                max-width: 1200px;
                margin: 0 auto;
            }

            .how-it-works-header {
                text-align: center;
                margin-bottom: 5rem;
            }

            .how-it-works-title {
                font-size: 2.75rem;
                font-weight: 800;
                color: var(--dark);
                margin-bottom: 1.25rem;
                letter-spacing: -1px;
            }

            .how-it-works-subtitle {
                font-size: 1.1rem;
                color: var(--gray);
                max-width: 600px;
                margin: 0 auto;
                line-height: 1.7;
            }

            .how-it-works-steps {
                display: flex;
                flex-direction: column;
                gap: 5rem;
            }

            .step-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 4rem;
                align-items: center;
                transition: var(--transition-normal);
            }

            .step-row:hover {
                transform: translateY(-4px);
            }

            .step-row.reverse {
                direction: rtl;
            }

            .step-row.reverse > * {
                direction: ltr;
            }

            .step-image {
                width: 100%;
                border-radius: 20px;
                overflow: hidden;
                height: 480px;
                box-shadow: var(--shadow-lg);
                position: relative;
            }

            .step-image::after {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(180deg, transparent 70%, rgba(0,0,0,0.1) 100%);
                pointer-events: none;
            }

            .step-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: var(--transition-slow);
            }

            .step-row:hover .step-image img {
                transform: scale(1.03);
            }

            .step-content {
                padding: 2rem 0;
            }

            .step-number {
                font-size: 0.8rem;
                font-weight: 700;
                color: var(--primary);
                margin-bottom: 1rem;
                text-transform: uppercase;
                letter-spacing: 2px;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
            }

            .step-number::before {
                content: '';
                width: 24px;
                height: 2px;
                background: linear-gradient(90deg, var(--primary), var(--secondary));
                border-radius: 2px;
            }

            .step-title {
                font-size: 1.85rem;
                font-weight: 700;
                color: var(--dark);
                margin-bottom: 1.25rem;
                line-height: 1.25;
                letter-spacing: -0.5px;
            }

            .step-description {
                font-size: 1.05rem;
                color: var(--gray);
                line-height: 1.75;
            }

            @media (max-width: 768px) {
                .how-it-works-section {
                    padding: 4rem 1.5rem;
                }

                .how-it-works-title {
                    font-size: 2rem;
                }

                .how-it-works-steps {
                    gap: 3rem;
                }

                .step-row {
                    grid-template-columns: 1fr;
                    gap: 2rem;
                }

                .step-row.reverse {
                    direction: ltr;
                }

                .step-image {
                    aspect-ratio: 4/5;
                }

                .step-title {
                    font-size: 1.5rem;
                }

                .step-description {
                    font-size: 1rem;
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
        <section class="hero">
            <!-- Icônes de nourriture animées en arrière-plan -->
            <div class="food-icons-background">
                <!-- Burger -->
                <div class="food-icon" style="top: 10%; left: 5%; animation-delay: 0s;">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M5 13l4 0l0 -4l-4 0z" />
                        <path d="M5 17l4 0l0 -2l-4 0z" />
                        <path d="M11 5l0 14" />
                        <path d="M15 5l0 14" />
                        <path d="M9 9h10" />
                        <path d="M9 13h10" />
                        <path d="M9 17h10" />
                    </svg>
                </div>

                <!-- Pizza -->
                <div class="food-icon" style="top: 70%; left: 10%; animation-delay: 1s;">
                    <svg width="70" height="70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M12 21.5c-3.04 0-5.952-.87-8.5-2.5l3.5-6.5 5 9.5z" />
                        <path d="M12 11.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" />
                        <path d="M8 11.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" />
                    </svg>
                </div>

                <!-- Fourchette et Couteau -->
                <div class="food-icon" style="top: 20%; right: 8%; animation-delay: 2s;">
                    <svg width="55" height="55" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M3 3l0 7a3 3 0 0 0 3 3h1l0 8" />
                        <path d="M9 3l0 11" />
                        <path d="M15 3l0 18" />
                        <path d="M21 3v7a3 3 0 0 1-3 3" />
                    </svg>
                </div>

                <!-- Plat chaud -->
                <div class="food-icon" style="top: 60%; right: 15%; animation-delay: 0.5s;">
                    <svg width="65" height="65" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M4 8h16a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2z" />
                        <path d="M8 4v4" />
                        <path d="M12 4v4" />
                        <path d="M16 4v4" />
                    </svg>
                </div>

                <!-- Café/Boisson -->
                <div class="food-icon" style="top: 40%; left: 15%; animation-delay: 1.5s;">
                    <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M3 14c.83 .642 2.077 1.017 3.5 1 1.423 .017 2.67 -.358 3.5 -1 .83 -.642 2.077 -1.017 3.5 -1 1.423 -.017 2.67 .358 3.5 1" />
                        <path d="M8 3a2.4 2.4 0 0 0 -1 2a2.4 2.4 0 0 0 1 2" />
                        <path d="M12 3a2.4 2.4 0 0 0 -1 2a2.4 2.4 0 0 0 1 2" />
                        <path d="M3 10h14v5a6 6 0 0 1 -6 6h-2a6 6 0 0 1 -6 -6v-5z" />
                        <path d="M16.746 16.726a3 3 0 1 0 .252 -5.555" />
                    </svg>
                </div>

                <!-- Sandwich -->
                <div class="food-icon" style="top: 80%; right: 25%; animation-delay: 2.5s;">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M4 11v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4" />
                        <path d="M4 11l3-3h10l3 3" />
                        <path d="M12 8v8" />
                    </svg>
                </div>

                <!-- Fruit -->
                <div class="food-icon" style="top: 30%; left: 25%; animation-delay: 3s;">
                    <svg width="55" height="55" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M12 3c-4.4 0-8 3.6-8 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8z" />
                        <path d="M12 3v4" />
                    </svg>
                </div>

                <!-- Salade -->
                <div class="food-icon" style="top: 15%; right: 30%; animation-delay: 1.8s;">
                    <svg width="58" height="58" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M4 11h16a1 1 0 0 1 1 1v.5c0 1.5-2.517 5.573-4 6.5v1a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1v-1c-1.687-1.054-4-5-4-6.5v-.5a1 1 0 0 1 1-1z" />
                        <path d="M12 4c0 1.5 2 2 2 4s-1 3-3 3-3-1-3-3 2-2.5 2-4" />
                    </svg>
                </div>

                <!-- Dessert/Gâteau -->
                <div class="food-icon" style="top: 50%; right: 5%; animation-delay: 2.2s;">
                    <svg width="62" height="62" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M3 20h18v-8a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3v8z" />
                        <path d="M3 14.803c.312.135.654.204 1 .197a2.4 2.4 0 0 0 2-1 2.4 2.4 0 0 1 2-1 2.4 2.4 0 0 1 2 1 2.4 2.4 0 0 0 2 1 2.4 2.4 0 0 0 2-1 2.4 2.4 0 0 1 2-1 2.4 2.4 0 0 1 2 1 2.4 2.4 0 0 0 2 1c.35.007.692-.062 1-.197" />
                        <path d="M12 4l1.465 1.638a2 2 0 1 1-3.015.099z" />
                    </svg>
                </div>

                <!-- Bol de riz/nouilles -->
                <div class="food-icon" style="top: 65%; left: 30%; animation-delay: 3.5s;">
                    <svg width="68" height="68" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M4 11h16a1 1 0 0 1 1 1v.5c0 1.5-2.517 5.573-4 6.5v1a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1v-1c-1.687-1.054-4-5-4-6.5v-.5a1 1 0 0 1 1-1z" />
                        <path d="M12 4c.5 1.333 1 2.333 2 3s3 1 4 2" />
                    </svg>
                </div>
            </div>

            <div class="hero-content">
                <h1 class="hero-title">Domini, votre nouvelle façon de déjeuner</h1>
                <p class="hero-subtitle">
                    La liberté de commander n'importe où sans payer pour le stationnement, le carburant ou l'entretien. 
                    Tout ce dont vous avez besoin, c'est d'une application et d'un casier intelligent.
                </p>
                <div class="hero-buttons">
                    <a href="#" class="btn-primary">Télécharger Domini</a>
                    <a href="#" class="btn-secondary">Commander des caisiers</a>
                </div>
            </div>
        </section>

        <!-- Image Section -->
        <section class="image-section">
            <div class="image-container">
                <img src="{{ asset('diverse-team-chefs-making-meal-preparations-with-ingredients-cooking-delicious-gourmet-dish-restaurant-kitchen-people-uniform-working-as-cooks-preparing-gastronomy-food-recipe.jpg') }}" alt="Repas frais et sain" class="featured-image" fetchpriority="high" decoding="async">
            </div>
        </section>

        <!-- Services Section -->
        <section class="services-section">
            <div class="services-header animate-on-scroll fade-in-up">
                <h2 class="services-title">Nos services</h2>
                <p class="services-subtitle">
                    Les produits et les fonctionnalités varient selon les pays. Certaines fonctionnalités listées ici peuvent ne pas être disponibles dans votre appli.
                </p>
            </div>

            <div class="services-slider-container">
                <div class="services-slider" id="servicesSlider">
                    <!-- Service 1: Commandes -->
                    <div class="service-card dark">
                        <img src="{{ asset('slide/person-putting-meat-salad-plate.jpg') }}" alt="Commandes" class="service-card-bg" loading="lazy" decoding="async">
                        <div class="service-card-overlay">
                            <div class="service-card-content">
                                <h3>Commandes</h3>
                                <p>Quelques secondes pour commander et quelques minutes pour récupérer.</p>
                            </div>
                            <a href="#" class="service-card-btn">Commander</a>
                        </div>
                    </div>

                    <!-- Service 2: Livraison -->
                    <div class="service-card">
                        <img src="{{ asset('slide/woman-holding-locally-grown-produce.jpg') }}" alt="Livraison" class="service-card-bg" loading="lazy" decoding="async">
                        <div class="service-card-overlay">
                            <div class="service-card-content">
                                <h3>Livraison</h3>
                                <p>Vos plats préférés, livrés rapidement.</p>
                            </div>
                            <a href="#" class="service-card-btn">Voir Domini Food</a>
                        </div>
                    </div>

                    <!-- Service 3: Casiers Intelligents -->
                    <div class="service-card">
                        <img src="{{ asset('slide/beautiful-young-woman-shopping-food.jpg') }}" alt="Casiers Intelligents" class="service-card-bg" loading="lazy" decoding="async">
                        <div class="service-card-overlay">
                            <div class="service-card-content">
                                <h3>Casiers Intelligents</h3>
                                <p>Récupération 24/7 avec votre carte NFC.</p>
                            </div>
                            <a href="#" class="service-card-btn white">Voir Domini Lockers</a>
                        </div>
                    </div>

                 

                    <!-- Service 5: Entreprise -->
                    <div class="service-card">
                        <img src="{{ asset('slide/advisory-board-members-meeting-boardroom-establish-future-development-plan.jpg') }}" alt="Entreprise" class="service-card-bg" loading="lazy" decoding="async">
                        <div class="service-card-overlay">
                            <div class="service-card-content">
                                <h3>Entreprise</h3>
                                <p>Gérez les trajets de votre équipe facilement.</p>
                            </div>
                            <a href="#" class="service-card-btn">Voir Domini Business</a>
                        </div>
                    </div>
                </div>

                <div class="slider-controls">
                    <button class="slider-btn" onclick="scrollSlider(-1)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 18l-6-6 6-6"/>
                        </svg>
                    </button>
                    <button class="slider-btn" onclick="scrollSlider(1)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 18l6-6-6-6"/>
                        </svg>
                    </button>
                </div>
            </div>
        </section>

        <!-- Impact Section -->
        <section class="impact-section">
            <div class="impact-container">
                <!-- Colonne gauche : Textes -->
                <div class="impact-slider-wrapper">
                    <div class="impact-slider">
                        <!-- Slide 1 -->
                        <div class="impact-slide active">
                            <div class="impact-content">
                                <h2 class="impact-title">Des repas savoureux, accessibles à tous vos employés.</h2>
                                <p class="impact-subtitle">
                                    Domini révolutionne la pause déjeuner en entreprise avec des repas de qualité, subventionnés par votre société et livrés directement dans vos locaux.
                                </p>
                                <div class="impact-feature">
                                    <h3>Une pause déjeuner réinventée</h3>
                                    <p>
                                        Fini les files d'attente et les déplacements ! Vos employés récupèrent leurs repas en quelques secondes dans nos casiers intelligents installés dans votre entreprise.
                                    </p>
                                    <a href="#comment-ca-marche" class="impact-link">Découvrir le concept</a>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 2 -->
                        <div class="impact-slide">
                            <div class="impact-content">
                                <h2 class="impact-title">Plus de temps pour ce qui compte vraiment.</h2>
                                <p class="impact-subtitle">
                                    Avec Domini, vos employés gagnent en moyenne 45 minutes par jour en évitant les déplacements et l'attente au restaurant.
                                </p>
                                <div class="impact-feature">
                                    <h3>Productivité et bien-être</h3>
                                    <p>
                                        Nos casiers intelligents permettent aux employés de récupérer leur repas à l'heure qui leur convient, sans contrainte ni stress. Plus de temps pour se détendre ou être productif.
                                    </p>
                                    <a href="/inscription" class="impact-link">Équiper mon entreprise</a>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 3 -->
                        <div class="impact-slide">
                            <div class="impact-content">
                                <h2 class="impact-title">Un avantage social qui fait la différence.</h2>
                                <p class="impact-subtitle">
                                    Offrez à vos employés l'accès à des repas de qualité pour 1500 à 3000 FCFA, avec une subvention totale ou partielle financée par votre entreprise.
                                </p>
                                <div class="impact-feature">
                                    <h3>Fidélisez vos talents</h3>
                                    <p>
                                        92% des employés considèrent les avantages repas comme un critère important dans le choix de leur entreprise. Avec Domini, vous investissez dans le bien-être de vos équipes.
                                    </p>
                                    <a href="/support" class="impact-link">En savoir plus</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="impact-controls">
                        <div class="impact-dots">
                            <span class="impact-dot active" onclick="goToSlide(0)"></span>
                            <span class="impact-dot" onclick="goToSlide(1)"></span>
                            <span class="impact-dot" onclick="goToSlide(2)"></span>
                        </div>
                    </div>
                </div>

                <!-- Colonne droite : Image -->
                <div class="impact-image-wrapper">
                    <img src="{{ asset('63378f21-e274-4597-8154-77207915e234-removebg-preview.png') }}" alt="Casier Domini" class="impact-image" loading="lazy" decoding="async">
                </div>
            </div>
        </section>

        <!-- Menu Showcase Section -->
        <section class="menu-showcase-section">
            <!-- Grid d'images -->
            <div class="menu-images-grid">
                <div class="menu-grid-item">
                    <img src="{{ asset('menu/big-sandwich-hamburger-with-juicy-beef-burger-cheese-tomato-red-onion-french-fries.jpg') }}" alt="Burger" loading="lazy" decoding="async">
                </div>
                <div class="menu-grid-item">
                    <img src="{{ asset('menu/fettuccine-pasta-with-meatballs-tomato-sauce.jpg') }}" alt="Pasta" loading="lazy" decoding="async">
                </div>
                <div class="menu-grid-item">
                    <img src="{{ asset('menu/side-view-pilaf-with-stewed-beef-meat-plate.jpg') }}" alt="Pilaf" loading="lazy" decoding="async">
                </div>
                <div class="menu-grid-item">
                    <img src="{{ asset('menu/chole-bhature-delicious-indian-street-food.jpg') }}" alt="Cuisine indienne" loading="lazy" decoding="async">
                </div>
                <div class="menu-grid-item">
                    <img src="{{ asset('menu/closeup-roasted-meat-with-sauce-vegetables-fries-plate-table.jpg') }}" alt="Viande rôtie" loading="lazy" decoding="async">
                </div>
                <div class="menu-grid-item">
                    <img src="{{ asset('menu/asian-food-restaurant.jpg') }}" alt="Cuisine asiatique" loading="lazy" decoding="async">
                </div>
            </div>

            <!-- Contenu texte -->
            <div class="menu-content-area">
                <div class="menu-content-container animate-on-scroll fade-in-up">
                    <div class="menu-label">Notre Menu</div>
                    <h2 class="menu-main-title">Découvrez notre menu varié et savoureux</h2>
                    <p class="menu-description">
                        Des plats cuisinés quotidiennement par nos chefs, de 1 500 à 3 000 FCFA. Chaque employé bénéficie d'un plat par jour, subventionné en totalité ou en partie par son entreprise. Une alternative moderne et pratique à la cantine traditionnelle.
                    </p>
                    <a href="{{ url('/notre-menu') }}" class="menu-btn">Voir notre menu</a>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="how-it-works-section" id="comment-ca-marche">
            <div class="how-it-works-container">
                <div class="how-it-works-header animate-on-scroll fade-in-up">
                    <h2 class="how-it-works-title">Comment ça marche</h2>
                    <p class="how-it-works-subtitle">
                        Domini simplifie la livraison de repas pour vos employés en 5 étapes simples.
                    </p>
                </div>

                <div class="how-it-works-steps">
                    <!-- Étape 1 -->
                    <div class="step-row animate-on-scroll fade-in-left">
                        <div class="step-image">
                            <img src="{{ asset('slide/beautiful-young-woman-shopping-food.jpg')  }}" alt="Étape 1" loading="lazy" decoding="async">
                        </div>
                        <div class="step-content">
                            <div class="step-number">Étape 1</div>
                            <h3 class="step-title">Votre entreprise commande des casiers et souscrit</h3>
                            <p class="step-description">
                                Votre employeur s'inscrit à Domini et commande des casiers intelligents pour faciliter la livraison et le retrait de vos repas en toute sécurité.
                            </p>
                        </div>
                    </div>

                    <!-- Étape 2 -->
                    <div class="step-row reverse animate-on-scroll fade-in-right">
                        <div class="step-image">
                            <img src="{{ asset('sectionlast/high-protein-meal-with-smartphone-arrangement.jpg') }}" alt="Étape 2" loading="lazy" decoding="async">
                        </div>
                        <div class="step-content">
                            <div class="step-number">Étape 2</div>
                            <h3 class="step-title">Téléchargez notre app et inscrivez-vous</h3>
                            <p class="step-description">
                                Téléchargez l'application Domini sur votre smartphone, créez votre compte et accédez instantanément à notre menu varié de plats cuisinés par nos chefs.
                            </p>
                        </div>
                    </div>

                    <!-- Étape 3 -->
                    <div class="step-row animate-on-scroll fade-in-left delay-100">
                        <div class="step-image">
                            <img src="{{ asset('sectionlast/menutelphone.jfif') }}" alt="Étape 3" loading="lazy" decoding="async">
                        </div>
                        <div class="step-content">
                            <div class="step-number">Étape 3</div>
                            <h3 class="step-title">Passez commande</h3>
                            <p class="step-description">
                                Parcourez le menu, choisissez vos plats préférés et passez commande en quelques clics. C'est simple, rapide et pratique.
                            </p>
                        </div>
                    </div>

                    <!-- Étape 4 -->
                    <div class="step-row reverse animate-on-scroll fade-in-right delay-200">
                        <div class="step-image">
                            <img src="{{ asset('sectionlast/Delivery guy.jfif') }}" alt="Étape 4" loading="lazy" decoding="async">
                        </div>
                        <div class="step-content">
                            <div class="step-number">Étape 4</div>
                            <h3 class="step-title">Le livreur livre et dépose vos plats dans votre box</h3>
                            <p class="step-description">
                                Notre livreur récupère votre commande et la dépose directement dans votre casier personnel sécurisé au sein de votre entreprise.
                            </p>
                        </div>
                    </div>

                    <!-- Étape 5 -->
                    <div class="step-row animate-on-scroll fade-in-left delay-300">
                        <div class="step-image">
                            <img src="{{ asset('sectionlast/6 Benefits Of Using NFC In Business_ _ Nexqo.jfif') }}" alt="Étape 5" loading="lazy" decoding="async">
                        </div>
                        <div class="step-content">
                            <div class="step-number">Étape 5</div>
                            <h3 class="step-title">Déverrouillez votre casier avec une carte NFC</h3>
                            <p class="step-description">
                                Utilisez votre carte NFC personnelle pour déverrouiller votre casier et récupérer votre repas chaud, prêt à être dégusté.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script>
            // ============================================
            // SYSTÈME D'ANIMATIONS AU SCROLL
            // ============================================

            // Intersection Observer pour les animations au scroll
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.1
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animated');
                        // Optionnel: arrêter d'observer après l'animation
                        // observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Observer tous les éléments animables
            document.addEventListener('DOMContentLoaded', () => {
                const animatedElements = document.querySelectorAll('.animate-on-scroll');
                animatedElements.forEach(el => observer.observe(el));

                // Navigation avec effet de scroll
                const nav = document.querySelector('nav');
                window.addEventListener('scroll', () => {
                    if (window.scrollY > 50) {
                        nav.classList.add('scrolled');
                    } else {
                        nav.classList.remove('scrolled');
                    }
                });

                // Auto-play du carousel impact
                let autoPlayInterval = setInterval(() => {
                    changeSlide(1);
                }, 5000);

                // Pause auto-play au hover
                const impactSection = document.querySelector('.impact-section');
                if (impactSection) {
                    impactSection.addEventListener('mouseenter', () => {
                        clearInterval(autoPlayInterval);
                    });
                    
                    impactSection.addEventListener('mouseleave', () => {
                        autoPlayInterval = setInterval(() => {
                            changeSlide(1);
                        }, 5000);
                    });
                }

                // Smooth scroll pour les ancres
                document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                    anchor.addEventListener('click', function (e) {
                        e.preventDefault();
                        const target = document.querySelector(this.getAttribute('href'));
                        if (target) {
                            target.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    });
                });

                // Animation staggered sur les cartes de service
                const serviceCards = document.querySelectorAll('.service-card');
                serviceCards.forEach((card, index) => {
                    card.style.animationDelay = `${index * 0.1}s`;
                });
            });

            // ============================================
            // CAROUSEL IMPACT
            // ============================================

            let currentSlide = 0;
            const slides = document.querySelectorAll('.impact-slide');
            const dots = document.querySelectorAll('.impact-dot');

            function showSlide(index) {
                slides.forEach(slide => {
                    slide.classList.remove('active');
                    slide.style.transform = 'translateX(30px)';
                    slide.style.opacity = '0';
                });
                
                dots.forEach(dot => dot.classList.remove('active'));
                
                if (index >= slides.length) currentSlide = 0;
                if (index < 0) currentSlide = slides.length - 1;
                
                // Animation d'entrée fluide
                setTimeout(() => {
                    slides[currentSlide].style.transform = 'translateX(0)';
                    slides[currentSlide].style.opacity = '1';
                    slides[currentSlide].classList.add('active');
                }, 50);
                
                dots[currentSlide].classList.add('active');
            }

            function changeSlide(direction) {
                currentSlide += direction;
                showSlide(currentSlide);
            }

            function goToSlide(index) {
                currentSlide = index;
                showSlide(currentSlide);
            }

            // ============================================
            // SLIDER SERVICES
            // ============================================

            function scrollSlider(direction) {
                const slider = document.getElementById('servicesSlider');
                const scrollAmount = 350;
                slider.scrollBy({
                    left: direction * scrollAmount,
                    behavior: 'smooth'
                });
            }

            // ============================================
            // PARALLAX EFFECT (léger) — throttlé requestAnimationFrame
            // ============================================

            let parallaxTicking = false;
            window.addEventListener('scroll', () => {
                if (parallaxTicking) return;
                parallaxTicking = true;
                requestAnimationFrame(() => {
                    const scrolled = window.pageYOffset;

                    const foodIcons = document.querySelectorAll('.food-icon');
                    foodIcons.forEach((icon, index) => {
                        const speed = 0.1 + (index * 0.02);
                        const yPos = -(scrolled * speed);
                        icon.style.transform = `translateY(${yPos}px)`;
                    });

                    const featuredImage = document.querySelector('.featured-image');
                    if (featuredImage) {
                        const rect = featuredImage.getBoundingClientRect();
                        if (rect.top < window.innerHeight && rect.bottom > 0) {
                            const yPos = (window.innerHeight - rect.top) * 0.1;
                            featuredImage.style.transform = `translateY(${yPos}px) scale(1.05)`;
                        }
                    }
                    parallaxTicking = false;
                });
            }, { passive: true });
        </script>

        <!-- Smartsupp : chargé après le premier rendu pour ne pas bloquer le LCP -->
        <script type="text/javascript">
        (function () {
            function loadSmartsupp() {
                if (window.__smartsuppLoaded) return;
                window.__smartsuppLoaded = true;
                var _smartsupp = window._smartsupp || {};
                _smartsupp.key = '73234f96a43e1e6223c9bc16cc051c9a054376c2';
                window.smartsupp || (function (d) {
                    var s, c, o = window.smartsupp = function () { o._.push(arguments); }; o._ = [];
                    s = d.getElementsByTagName('script')[0]; c = d.createElement('script');
                    c.type = 'text/javascript'; c.charset = 'utf-8'; c.async = true;
                    c.src = 'https://www.smartsuppchat.com/loader.js?';
                    s.parentNode.insertBefore(c, s);
                })(document);
            }
            if ('requestIdleCallback' in window) {
                requestIdleCallback(loadSmartsupp, { timeout: 4000 });
            } else {
                window.addEventListener('load', function () { setTimeout(loadSmartsupp, 2000); });
            }
        })();
        </script>
        <noscript>Powered by <a href="https://www.smartsupp.com" target="_blank">Smartsupp</a></noscript>
    </body>
</html>



