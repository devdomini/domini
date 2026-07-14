<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscription Entreprise - Domini</title>
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

        /* Main Section */
        .inscription-section {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            padding-top: 80px;
        }

        .left-content {
            background-image: url('{{ asset("menu/closeup-roasted-meat-with-sauce-vegetables-fries-plate-table.jpg") }}');
            background-size: cover;
            background-position: center;
            position: relative;
            padding: 4rem;
            display: flex;
            align-items: center;
        }

        .left-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 0, 0, 0.85), rgba(0, 0, 0, 0.7));
        }

        .left-text {
            position: relative;
            z-index: 1;
            color: white;
            max-width: 600px;
        }

        .left-text h1 {
            font-size: 3rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 2rem;
            letter-spacing: -2px;
        }

        .left-text p {
            font-size: 1.25rem;
            line-height: 1.6;
            opacity: 0.95;
        }

        .right-content {
            background-color: #FFFFFF;
            padding: 4rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-container {
            max-width: 500px;
            width: 100%;
        }

        .form-header {
            margin-bottom: 3rem;
        }

        .form-header h2 {
            font-size: 2rem;
            font-weight: 900;
            color: #1A1A1A;
            margin-bottom: 0.5rem;
            letter-spacing: -1px;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.875rem;
            color: #1A1A1A;
            margin-bottom: 0.5rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 1rem;
            border: 2px solid #E5E5E5;
            border-radius: 8px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s;
            background-color: #F9F9F9;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #34A853;
            background-color: #FFFFFF;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .phone-input {
            display: grid;
            grid-template-columns: 120px 1fr;
            gap: 0.5rem;
        }

        .country-code {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem;
            border: 2px solid #E5E5E5;
            border-radius: 8px;
            background-color: #F9F9F9;
            font-size: 1rem;
        }

        .flag {
            font-size: 1.25rem;
        }

        .submit-btn {
            width: 100%;
            background-color: #34A853;
            color: white;
            padding: 1.125rem;
            border: none;
            border-radius: 8px;
            font-size: 1.125rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 1rem;
        }

        .submit-btn:hover {
            background-color: #2d9248;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 168, 83, 0.3);
        }

        .form-footer {
            margin-top: 1.5rem;
            font-size: 0.75rem;
            color: #666;
            text-align: center;
            line-height: 1.5;
        }

        .form-footer a {
            color: #34A853;
            text-decoration: none;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        .success-message {
            display: none;
            background-color: #E8F5E9;
            border: 2px solid #34A853;
            color: #2d9248;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .nav-content {
                padding: 1rem 1.5rem;
            }

            .nav-right > *:not(.menu-icon):not(.btn-signup) {
                display: none;
            }

            .inscription-section {
                grid-template-columns: 1fr;
            }

            .left-content {
                min-height: 400px;
                padding: 2rem;
            }

            .left-text h1 {
                font-size: 2rem;
            }

            .left-text p {
                font-size: 1rem;
            }

            .right-content {
                padding: 2rem 1.5rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }

        @media (min-width: 769px) {
            .menu-icon {
                display: none;
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
                <a href="#" class="nav-link">Télécharger l'application</a>
                <a href="{{ url('/inscription') }}" class="btn-signup">Inscrivez votre entreprise</a>
                <div class="menu-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Section -->
    <section class="inscription-section">
        <div class="left-content">
            <div class="left-text">
                <h1>Équipez votre entreprise avec Domini et améliorez la vie de vos employés</h1>
                <p>Les entreprises Domini bénéficient d'employés plus satisfaits, d'une meilleure productivité et d'une gestion simplifiée — inscrivez-vous aujourd'hui et profitez des avantages !</p>
            </div>
        </div>

        <div class="right-content">
            <div class="form-container">
                <div class="form-header">
                    <h2>Laissez-nous vous aider à faire croître votre entreprise !</h2>
                </div>

                <div class="success-message" id="successMessage">
                    ✓ Votre demande a été envoyée avec succès ! Notre équipe vous contactera sous 48h.
                </div>

                <form id="inscriptionForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="company">Nom de l'entreprise</label>
                            <input type="text" id="company" name="company" placeholder="Entrez le nom de l'entreprise" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Votre nom</label>
                            <input type="text" id="name" name="name" placeholder="Saisissez votre nom" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">Ville</label>
                            <select id="city" name="city" required>
                                <option value="">Ville</option>
                                <option value="abidjan">Abidjan</option>
                                <option value="bouake">Bouaké</option>
                                <option value="yamoussoukro">Yamoussoukro</option>
                                <option value="daloa">Daloa</option>
                                <option value="korhogo">Korhogo</option>
                                <option value="san-pedro">San-Pédro</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="address">Adresse</label>
                            <input type="text" id="address" name="address" placeholder="Saisissez l'adresse de votre établissement" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Saisissez votre adresse email" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Numéro de téléphone</label>
                        <div class="phone-input">
                            <div class="country-code">
                                <span class="flag">🇨🇮</span>
                                <span>+225</span>
                            </div>
                            <input type="tel" id="phone" name="phone" placeholder="Numéro de téléphone" required pattern="[0-9]{10}">
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">Commencer</button>

                    <div class="form-footer">
                        En vous inscrivant, vous acceptez nos <a href="#">conditions générales</a> et notre <a href="#">politique de confidentialité</a>.
                    </div>
                </form>
            </div>
        </div>
    </section>

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

        // Form submission
        document.getElementById('inscriptionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = {
                company: document.getElementById('company').value,
                name: document.getElementById('name').value,
                city: document.getElementById('city').value,
                address: document.getElementById('address').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value
            };

            // Show success message
            const successMessage = document.getElementById('successMessage');
            successMessage.style.display = 'block';
            
            // Reset form
            this.reset();
            
            // Scroll to success message
            successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Hide success message after 5 seconds
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 5000);

            // Here you would normally send the data to your backend
            console.log('Form submitted:', formData);
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
