<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Devenir Livreur - Domini</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

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
                --surface-red-light: #FFEBEE;
                --surface-red-mid: #FFCDD2;
                --surface-neutral: #E8E8E8;
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

            .nav-link.active {
                color: var(--primary);
                font-weight: 600;
            }

            .nav-link.active::after {
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
                min-height: 70vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 120px 2rem 60px;
                background: linear-gradient(180deg, var(--light) 0%, rgba(255,255,255,0.8) 100%);
                position: relative;
                overflow: hidden;
            }

            .hero::before {
                content: '';
                position: absolute;
                top: -30%;
                right: -10%;
                width: 60%;
                height: 120%;
                background: radial-gradient(ellipse at center, rgba(255, 0, 0, 0.04) 0%, transparent 70%);
                pointer-events: none;
            }

            .hero-content {
                max-width: 800px;
                margin: 0 auto;
                text-align: center;
                position: relative;
                z-index: 1;
            }

            .hero-title {
                font-size: 3.25rem;
                font-weight: 800;
                color: var(--dark);
                margin-bottom: 1.5rem;
                line-height: 1.15;
                letter-spacing: -1.5px;
                background: linear-gradient(135deg, var(--dark) 0%, var(--dark-light) 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .hero-subtitle {
                font-size: 1.15rem;
                color: var(--gray);
                margin-bottom: 2.5rem;
                font-weight: 500;
                max-width: 600px;
                margin-left: auto;
                margin-right: auto;
                line-height: 1.7;
            }

            .hero-cta {
                display: flex;
                gap: 1.25rem;
                justify-content: center;
                flex-wrap: wrap;
            }

            .btn-primary {
                background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
                color: var(--white);
                padding: 1rem 2.5rem;
                border-radius: 50px;
                text-decoration: none;
                font-weight: 600;
                font-size: 1.05rem;
                transition: var(--transition-normal);
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                box-shadow: var(--shadow-primary);
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
                background: linear-gradient(135deg, var(--dark-light) 0%, var(--dark) 100%);
                transform: translateY(-3px);
                box-shadow: var(--shadow-lg);
            }

            .btn-primary:hover::before {
                left: 100%;
            }

            /* Section générique améliorée */
            .section {
                padding: 6rem 2rem;
                background-color: var(--white);
            }

            .section-alt {
                background-color: var(--light);
            }

            .section-header {
                text-align: center;
                max-width: 700px;
                margin: 0 auto 4rem;
            }

            .section-title {
                font-size: 2.5rem;
                font-weight: 800;
                color: var(--dark);
                margin-bottom: 1rem;
                letter-spacing: -0.5px;
            }

            .section-description {
                font-size: 1.1rem;
                color: var(--gray);
                line-height: 1.75;
            }

            /* Requirements Section améliorée */
            .requirements-section {
                padding: 6rem 2rem;
                background: linear-gradient(180deg, var(--white) 0%, var(--light) 100%);
            }

            .requirements-container {
                max-width: 1200px;
                margin: 0 auto;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 5rem;
                align-items: center;
            }

            .requirements-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 2.5rem;
            }

            .requirement-card {
                text-align: center;
                padding: 1.5rem;
                border-radius: 16px;
                background-color: var(--white);
                box-shadow: var(--shadow-sm);
                transition: var(--transition-normal);
            }

            .requirement-card:hover {
                transform: translateY(-6px);
                box-shadow: var(--shadow-md);
            }

            .requirement-icon {
                margin-bottom: 1.25rem;
                display: flex;
                justify-content: center;
                align-items: center;
                width: 80px;
                height: 80px;
                margin-left: auto;
                margin-right: auto;
                background: linear-gradient(135deg, rgba(255, 0, 0, 0.06) 0%, rgba(0, 0, 0, 0.04) 100%);
                border-radius: 50%;
            }

            .requirement-icon svg {
                width: 40px;
                height: 40px;
                color: var(--primary);
                stroke-width: 1.5;
            }

            .requirement-text {
                font-size: 0.95rem;
                color: var(--dark);
                line-height: 1.65;
                font-weight: 500;
            }

            .requirements-image {
                width: 100%;
                height: 520px;
                border-radius: 24px;
                overflow: hidden;
                box-shadow: var(--shadow-lg);
                position: relative;
            }

            .requirements-image::after {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(180deg, transparent 60%, rgba(0,0,0,0.1) 100%);
                pointer-events: none;
            }

            .requirements-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: var(--transition-slow);
            }

            .requirements-image:hover img {
                transform: scale(1.03);
            }

            /* Process Section améliorée */
            .process-section {
                padding: 6rem 2rem;
                background: linear-gradient(180deg, var(--light) 0%, var(--white) 100%);
            }

            .process-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 2.5rem;
                max-width: 1200px;
                margin: 0 auto;
            }

            .process-card {
                text-align: center;
                padding: 2rem;
                border-radius: 20px;
                background-color: var(--white);
                box-shadow: var(--shadow-sm);
                transition: var(--transition-normal);
            }

            .process-card:hover {
                transform: translateY(-8px);
                box-shadow: var(--shadow-md);
            }

            .process-icon-wrapper {
                width: 160px;
                height: 160px;
                margin: 0 auto 2rem;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: var(--transition-normal);
                position: relative;
            }

            .process-icon-wrapper::after {
                content: '';
                position: absolute;
                inset: -6px;
                border-radius: 50%;
                border: 2px dashed transparent;
                transition: var(--transition-normal);
            }

            .process-card:hover .process-icon-wrapper::after {
                border-color: rgba(0,0,0,0.1);
            }

            .process-icon-wrapper svg {
                width: 70px;
                height: 70px;
                color: var(--dark-light);
                stroke-width: 1.5;
                transition: var(--transition-normal);
            }

            .process-card:hover .process-icon-wrapper svg {
                transform: scale(1.1);
            }

            .process-icon-wrapper.blue {
                background: linear-gradient(135deg, var(--surface-red-light) 0%, var(--surface-red-mid) 100%);
            }

            .process-icon-wrapper.yellow {
                background: linear-gradient(135deg, #FFE0E0 0%, #E57373 100%);
            }

            .process-icon-wrapper.pink {
                background: linear-gradient(135deg, var(--surface-neutral) 0%, #BDBDBD 100%);
            }

            .process-text {
                font-size: 0.95rem;
                color: var(--gray);
                line-height: 1.75;
                max-width: 280px;
                margin: 0 auto;
            }

            /* Testimonials Section améliorée */
            .testimonials-section {
                padding: 6rem 2rem;
                background: linear-gradient(180deg, var(--white) 0%, var(--light) 100%);
            }

            .testimonials-container {
                max-width: 1100px;
                margin: 0 auto;
                position: relative;
            }

            .testimonial-wrapper {
                display: flex;
                gap: 4rem;
                align-items: center;
                padding: 2rem;
                background-color: var(--white);
                border-radius: 24px;
                box-shadow: var(--shadow-md);
            }

            .testimonial-image {
                width: 380px;
                height: 380px;
                border-radius: 20px;
                overflow: hidden;
                flex-shrink: 0;
                box-shadow: var(--shadow-md);
                position: relative;
            }

            .testimonial-image::after {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(180deg, transparent 70%, rgba(0,0,0,0.15) 100%);
                pointer-events: none;
            }

            .testimonial-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: var(--transition-slow);
            }

            .testimonial-wrapper:hover .testimonial-image img {
                transform: scale(1.05);
            }

            .testimonial-content {
                flex: 1;
                padding: 1rem;
            }

            .testimonial-text {
                font-size: 1.15rem;
                color: var(--dark);
                line-height: 1.85;
                margin-bottom: 2rem;
                font-style: italic;
                position: relative;
                padding-left: 1.5rem;
            }

            .testimonial-text::before {
                content: '"';
                position: absolute;
                left: 0;
                top: -10px;
                font-size: 3rem;
                color: var(--primary);
                opacity: 0.3;
                font-family: Georgia, serif;
            }

            .testimonial-author {
                font-weight: 700;
                font-size: 1.1rem;
                color: var(--dark);
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            .testimonial-author::before {
                content: '';
                width: 24px;
                height: 2px;
                background: linear-gradient(90deg, var(--primary), var(--secondary));
                border-radius: 2px;
            }

            .testimonial-dots {
                display: flex;
                gap: 0.6rem;
                justify-content: center;
                margin-top: 2.5rem;
            }

            .testimonial-dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background-color: rgba(209, 213, 219, 0.6);
                cursor: pointer;
                transition: var(--transition-normal);
            }

            .testimonial-dot:hover {
                background-color: rgba(255, 0, 0, 0.5);
            }

            .testimonial-dot.active {
                background-color: var(--primary);
                width: 28px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(255, 0, 0, 0.3);
            }

            /* Benefits Section améliorée */
            .benefits-section {
                padding: 6rem 2rem;
                background: linear-gradient(180deg, var(--light) 0%, var(--white) 100%);
                position: relative;
            }

            .benefits-section::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 5px;
                background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            }

            .benefits-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 2rem;
                max-width: 1200px;
                margin: 0 auto;
            }

            .benefit-card {
                background-color: var(--white);
                padding: 2.5rem;
                border-radius: 24px;
                text-align: center;
                transition: var(--transition-normal);
                position: relative;
                overflow: hidden;
            }

            .benefit-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                opacity: 0;
                transition: var(--transition-normal);
            }

            .benefit-card.blue {
                background: linear-gradient(135deg, var(--surface-red-light) 0%, var(--surface-red-mid) 100%);
            }

            .benefit-card.yellow {
                background: linear-gradient(135deg, #FFF5F5 0%, #FFCDD2 100%);
            }

            .benefit-card.pink {
                background: linear-gradient(135deg, #F5F5F5 0%, #E0E0E0 100%);
            }

            .benefit-card:hover {
                transform: translateY(-10px) scale(1.02);
                box-shadow: var(--shadow-lg);
            }

            .benefit-icon {
                margin-bottom: 1.5rem;
                display: flex;
                justify-content: center;
                align-items: center;
                width: 80px;
                height: 80px;
                margin-left: auto;
                margin-right: auto;
                background-color: rgba(255,255,255,0.5);
                border-radius: 50%;
                transition: var(--transition-normal);
            }

            .benefit-card:hover .benefit-icon {
                transform: scale(1.1) rotate(5deg);
            }

            .benefit-icon svg {
                width: 40px;
                height: 40px;
                color: var(--dark-light);
                stroke-width: 1.5;
            }

            .benefit-title {
                font-size: 1.25rem;
                font-weight: 700;
                color: var(--dark);
                margin-bottom: 1rem;
            }

            .benefit-description {
                font-size: 0.95rem;
                color: var(--dark);
                line-height: 1.7;
                opacity: 0.85;
            }

            /* CTA Section améliorée */
            .cta-section {
                min-height: 550px;
                display: grid;
                grid-template-columns: 1fr 1fr;
                position: relative;
                overflow: hidden;
            }

            .cta-left {
                background: linear-gradient(180deg, var(--white) 0%, var(--light) 100%);
                padding: 5rem 4rem;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .cta-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                background: linear-gradient(135deg, rgba(255, 0, 0, 0.06) 0%, rgba(0, 0, 0, 0.04) 100%);
                color: var(--primary);
                padding: 0.5rem 1.25rem;
                border-radius: 50px;
                font-weight: 600;
                font-size: 0.85rem;
                margin-bottom: 1.5rem;
                width: fit-content;
                border: 1px solid rgba(255, 0, 0, 0.15);
            }

            .cta-title {
                font-size: 2.25rem;
                font-weight: 800;
                color: var(--dark);
                margin-bottom: 1.25rem;
                line-height: 1.2;
                letter-spacing: -0.5px;
            }

            .cta-subtitle {
                font-size: 1.05rem;
                color: var(--gray);
                margin-bottom: 2rem;
                line-height: 1.7;
            }

            .cta-button {
                background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
                color: var(--white);
                padding: 1.1rem 2.5rem;
                border-radius: 50px;
                text-decoration: none;
                font-weight: 600;
                font-size: 1.05rem;
                transition: var(--transition-normal);
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                width: fit-content;
                border: none;
                cursor: pointer;
                box-shadow: var(--shadow-primary);
            }

            .cta-button:hover {
                background: linear-gradient(135deg, var(--dark-light) 0%, var(--dark) 100%);
                transform: translateY(-3px);
                box-shadow: var(--shadow-lg);
            }

            .cta-right {
                background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
                position: relative;
                clip-path: polygon(12% 0, 100% 0, 100% 100%, 0 100%);
            }

            .cta-right img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                opacity: 0.85;
                transition: var(--transition-slow);
            }

            .cta-right:hover img {
                opacity: 0.95;
                transform: scale(1.02);
            }

            /* Footer amélioré */
            .footer {
                background: linear-gradient(180deg, var(--dark) 0%, #0a0a0a 100%);
                color: var(--white);
                padding: 3rem 2rem 2rem;
                text-align: center;
            }

            .footer-text {
                font-size: 0.95rem;
                color: rgba(255,255,255,0.6);
            }

            .footer-links {
                display: flex;
                gap: 2rem;
                justify-content: center;
                margin-top: 1.5rem;
                flex-wrap: wrap;
            }

            .footer-link {
                color: rgba(255,255,255,0.8);
                text-decoration: none;
                transition: var(--transition-normal);
                font-size: 0.9rem;
            }

            .footer-link:hover {
                color: var(--primary);
            }

            /* Responsive amélioré */
            @media (max-width: 768px) {
                .nav-content {
                    padding: 1rem 1.5rem;
                }

                .nav-right > *:not(.menu-icon):not(.btn-signup) {
                    display: none;
                }

                .hero {
                    padding: 120px 1.5rem 60px;
                    min-height: 60vh;
                }

                .hero-title {
                    font-size: 2.25rem;
                    letter-spacing: -0.5px;
                }

                .hero-subtitle {
                    font-size: 1rem;
                }

                .hero-cta {
                    flex-direction: column;
                    align-items: center;
                    gap: 1rem;
                }

                .btn-primary {
                    width: 100%;
                    justify-content: center;
                }

                .section-title {
                    font-size: 1.85rem;
                }

                .section-header {
                    margin-bottom: 3rem;
                }

                .requirements-section,
                .process-section,
                .benefits-section,
                .testimonials-section {
                    padding: 4rem 1.5rem;
                }

                .requirements-container {
                    grid-template-columns: 1fr;
                    gap: 3rem;
                }

                .requirements-grid {
                    grid-template-columns: 1fr;
                    gap: 1.5rem;
                }

                .requirements-image {
                    height: 320px;
                }

                .process-grid {
                    grid-template-columns: 1fr;
                    gap: 1.5rem;
                }

                .process-icon-wrapper {
                    width: 130px;
                    height: 130px;
                }

                .process-icon-wrapper svg {
                    width: 55px;
                    height: 55px;
                }

                .testimonial-wrapper {
                    flex-direction: column;
                    gap: 2rem;
                    padding: 1.5rem;
                }

                .testimonial-image {
                    width: 100%;
                    height: 280px;
                }

                .testimonial-text {
                    font-size: 1.05rem;
                }

                .benefits-grid {
                    grid-template-columns: 1fr;
                    gap: 1.5rem;
                }

                .benefit-card {
                    padding: 2rem;
                }

                .cta-section {
                    grid-template-columns: 1fr;
                }

                .cta-right {
                    clip-path: none;
                    min-height: 280px;
                }

                .cta-left {
                    padding: 3rem 1.5rem;
                }

                .cta-title {
                    font-size: 1.75rem;
                }
            }

            @media (min-width: 769px) {
                .menu-icon {
                    display: none;
                }
            }

            /* Animations d'apparition */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-on-scroll {
                opacity: 0;
                animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            }

            .delay-100 { animation-delay: 0.1s; }
            .delay-200 { animation-delay: 0.2s; }
            .delay-300 { animation-delay: 0.3s; }
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
                    <a href="{{ url('/devenir-livreur') }}" class="nav-link active">Devenir livreur</a>
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
            <div class="hero-content">
                <h1 class="hero-title">Devenez livreur Domini</h1>
                <p class="hero-subtitle">
                    Gagnez un revenu flexible en livrant des repas de qualité aux entreprises de votre ville
                </p>
                <div class="hero-cta">
                    <a href="#postuler" class="btn-primary">Postuler maintenant</a>
                </div>
            </div>
        </section>

  <!-- CTA Section -->

  <!-- Requirements Section -->
  <section class="requirements-section">
            <div class="section-header">
                <h2 class="section-title">Pour devenir livreur, il faut</h2>
            </div>

            <div class="requirements-container">
                <div class="requirements-grid">
                    <div class="requirement-card">
                        <div class="requirement-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 8v8"/>
                                <path d="M8 12h8"/>
                                <circle cx="9" cy="9" r="1" fill="currentColor"/>
                                <circle cx="15" cy="9" r="1" fill="currentColor"/>
                                <path d="M8 16c1 1 2 1.5 4 1.5s3-.5 4-1.5"/>
                            </svg>
                        </div>
                        <p class="requirement-text">Une preuve d'identité et un permis de travail valide.</p>
                    </div>

                    <div class="requirement-card">
                        <div class="requirement-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <p class="requirement-text">Une preuve que tu as au moins 18 ans.</p>
                    </div>

                    <div class="requirement-card">
                        <div class="requirement-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                            </svg>
                        </div>
                        <p class="requirement-text">Être autonome et avoir une attitude positive et volontaire.</p>
                    </div>

                    <div class="requirement-card">
                        <div class="requirement-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                                <path d="M9 12l2 2 4-4"/>
                            </svg>
                        </div>
                        <p class="requirement-text">Aimer travailler en extérieur !</p>
                    </div>
                </div>

                <div class="requirements-image">
                    <img src="{{ asset('livreur/young-african-guy-accepts-order-by-phone-write-motorbike-holding-boxes-with-pizza.jpg') }}" alt="Livreur Domini">
                </div>
            </div>
        </section>
   <!-- Benefits Section -->
   <section class="benefits-section">
            <div class="section-header">
                <h2 class="section-title">Ce que nous offrons</h2>
            </div>

            <div class="benefits-grid">
                <div class="benefit-card blue">
                    <div class="benefit-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                            <line x1="8" y1="21" x2="16" y2="21"/>
                            <line x1="12" y1="17" x2="12" y2="21"/>
                        </svg>
                    </div>
                    <h3 class="benefit-title">Qu'attends-tu ?</h3>
                    <p class="benefit-description">
                        Travaille dans le cadre d'un CDI qui te convient au mieux et touche un salaire horaire assorti de primes. Nous te paierons même lorsque tu attends les commandes.
                    </p>
                </div>

                <div class="benefit-card yellow">
                    <div class="benefit-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <circle cx="12" cy="12" r="6"/>
                            <circle cx="12" cy="12" r="2"/>
                        </svg>
                    </div>
                    <h3 class="benefit-title">Rejoignez l’équipe Domini</h3>
                    <p class="benefit-description">
                        Un environnement de travail stable et sécurisé et de nombreux avantages. Ton parcours professionnel commence ici.
                    </p>
                </div>

                <div class="benefit-card pink">
                    <div class="benefit-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                    </div>
                    <h3 class="benefit-title">Nous sommes là pour toi</h3>
                    <p class="benefit-description">
                        Les shifts qui te conviennent, une formation à la sécurité, le équipement gratuit et une assistance sur demande. Nous avons précisément ce qu'il te faut, quand il te le faut.
                    </p>
                </div>
            </div>
        </section>

      

        <!-- Process Section -->
     
        <!-- Testimonials Section -->
        <section class="testimonials-section">
            <div class="section-header">
                <h2 class="section-title">Témoignages</h2>
            </div>

            <div class="testimonials-container">
                <div class="testimonial-wrapper">
                    <div class="testimonial-image">
                        <img src="{{ asset('livreur/food-delivery-boy-driving-scooter-with-box-with-food-wearing-mask.jpg') }}" alt="Témoignage livreur">
                    </div>
                    <div class="testimonial-content">
                        <p class="testimonial-text">
                            "J'adore travailler chez Domini à cause de l'atmosphère de travail super cool. En outre, l'équipe de Domini est très proche et la coopération entre les livreurs est également très agréable."
                        </p>
                        <p class="testimonial-author">Kouadio Jean</p>
                    </div>
                </div>

                <div class="testimonial-dots">
                    <span class="testimonial-dot active"></span>
                    <span class="testimonial-dot"></span>
                    <span class="testimonial-dot"></span>
                </div>
            </div>
        </section>
        <section class="process-section">
            <div class="section-header">
                <h2 class="section-title">La procédure de recrutement</h2>
            </div>

            <div class="process-grid">
                <div class="process-card">
                    <div class="process-icon-wrapper blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
                            <line x1="12" y1="18" x2="12.01" y2="18"/>
                        </svg>
                    </div>
                    <p class="process-text">
                        Tout d'abord, nous devons savoir comment tu aimes travailler. Pour cela, nous aurons besoin de quelques documents de ta part, puis il y aura un entretien téléphonique.
                    </p>
                </div>

                <div class="process-card">
                    <div class="process-icon-wrapper yellow">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                    </div>
                    <p class="process-text">
                        Nous te contacterons au sujet du contrat de travail. Après l'avoir signé, tu apprendras à utiliser les outils et à réserver ton premier créneau horaire.
                    </p>
                </div>

                <div class="process-card">
                    <div class="process-icon-wrapper pink">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 7h-9"/>
                            <path d="M14 17H5"/>
                            <circle cx="17" cy="17" r="3"/>
                            <circle cx="7" cy="7" r="3"/>
                            <path d="M12 3 8 7l4 4"/>
                        </svg>
                    </div>
                    <p class="process-text">
                        Le premier jour, un capitaine t'aidera et t'expliquera tout ce que tu dois savoir pour livrer ta toute première commande.
                    </p>
                </div>
            </div>
        </section>


     
      
      
    </body>
</html>
