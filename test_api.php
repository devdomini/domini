<?php

/**
 * Script de test des APIs Domini
 * Génère un rapport détaillé de tous les endpoints
 * 
 * Usage: php test_api.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Configuration
$baseUrl = 'http://localhost:8000/api';
$results = [];
$authToken = null;
$testUser = null;

// Fonction pour faire une requête HTTP
function makeRequest($method, $url, $data = null, $headers = []) {
    $ch = curl_init();
    
    $defaultHeaders = [
        'Content-Type: application/json',
        'Accept: application/json',
    ];
    
    $headers = array_merge($defaultHeaders, $headers);
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 30,
    ]);
    
    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'status_code' => $httpCode,
        'response' => $response,
        'error' => $error,
        'success' => $httpCode >= 200 && $httpCode < 300,
    ];
}

// Fonction pour formater la réponse JSON
function formatResponse($response) {
    $decoded = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    return $response;
}

// Fonction pour enregistrer un test
function recordTest($name, $method, $endpoint, $data, $result, $description = '') {
    global $results;
    $results[] = [
        'name' => $name,
        'method' => $method,
        'endpoint' => $endpoint,
        'description' => $description,
        'request_data' => $data,
        'status_code' => $result['status_code'],
        'success' => $result['success'],
        'response' => formatResponse($result['response']),
        'error' => $result['error'],
        'timestamp' => date('Y-m-d H:i:s'),
    ];
}

echo "🚀 Démarrage des tests API Domini...\n\n";

// ============================================
// 1. TESTS D'AUTHENTIFICATION (Routes publiques)
// ============================================

echo "📝 Test 1: Inscription d'un nouvel utilisateur\n";
// Générer un email et téléphone uniques pour éviter les conflits
$timestamp = time();
$randomNum = rand(1000, 9999);
$registerData = [
    'name' => 'Test User API ' . $timestamp,
    'email' => 'testapi' . $timestamp . '@example.com',
    'telephone' => '+2250' . $randomNum . '6789',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'role' => 'employe',
];
$result = makeRequest('POST', "$baseUrl/auth/register", $registerData);
recordTest(
    'Inscription utilisateur',
    'POST',
    '/api/auth/register',
    $registerData,
    $result,
    'Créer un nouveau compte employé'
);

$testEmail = $registerData['email'];
$testTelephone = $registerData['telephone'];
$verificationCode = null;

if ($result['success']) {
    $responseData = json_decode($result['response'], true);
    // Gérer les deux formats: 'token' (ancien) et 'access_token' (nouveau)
    if (isset($responseData['data']['access_token'])) {
        $authToken = $responseData['data']['access_token'];
        $testUser = $responseData['data']['user'];
        echo "✅ Token obtenu: " . substr($authToken, 0, 20) . "...\n";
        if (isset($responseData['data']['expires_at'])) {
            echo "✅ Date d'expiration: " . $responseData['data']['expires_at'] . "\n";
        }
        echo "✅ Email utilisé: $testEmail\n";
        echo "✅ Téléphone utilisé: $testTelephone\n";
    } elseif (isset($responseData['data']['token'])) {
        $authToken = $responseData['data']['token'];
        $testUser = $responseData['data']['user'];
        echo "✅ Token obtenu: " . substr($authToken, 0, 20) . "...\n";
        echo "✅ Email utilisé: $testEmail\n";
        echo "✅ Téléphone utilisé: $testTelephone\n";
    }
    // Note: Le code de vérification n'est pas retourné pour des raisons de sécurité
    // Il faudrait le récupérer depuis la base de données ou les logs pour tester la vérification
} else {
    // Si l'inscription échoue, essayer de se connecter avec les identifiants existants
    $responseData = json_decode($result['response'], true);
    if (isset($responseData['errors']['email'])) {
        echo "⚠️  Utilisateur existe déjà, utilisation des identifiants existants\n";
        $testEmail = 'testapi@example.com';
        $testTelephone = '+2250123456789';
    }
}

echo "\n📝 Test 2: Connexion\n";
$loginData = [
    'email' => $testEmail,
    'password' => 'password123',
];
$result = makeRequest('POST', "$baseUrl/auth/login", $loginData);
recordTest(
    'Connexion',
    'POST',
    '/api/auth/login',
    $loginData,
    $result,
    'Se connecter avec email et mot de passe'
);

if ($result['success'] && !$authToken) {
    $responseData = json_decode($result['response'], true);
    // Gérer les deux formats: 'token' (ancien) et 'access_token' (nouveau)
    if (isset($responseData['data']['access_token'])) {
        $authToken = $responseData['data']['access_token'];
        if (isset($responseData['data']['expires_at'])) {
            echo "✅ Date d'expiration du token: " . $responseData['data']['expires_at'] . "\n";
        }
    } elseif (isset($responseData['data']['token'])) {
        $authToken = $responseData['data']['token'];
    }
}

echo "\n📝 Test 3: Mot de passe oublié\n";
$forgotPasswordData = ['email' => $testEmail];
$result = makeRequest('POST', "$baseUrl/auth/forgot-password", $forgotPasswordData);
recordTest(
    'Mot de passe oublié',
    'POST',
    '/api/auth/forgot-password',
    $forgotPasswordData,
    $result,
    'Demander la réinitialisation du mot de passe'
);

echo "\n📝 Test 4: Vérification téléphone\n";
// Note: Ce test nécessite un code réel envoyé par SMS
// Pour un test complet, il faudrait récupérer le code depuis la base de données
// Pour l'instant, on teste avec un code fictif pour vérifier la logique de validation
$verifyPhoneData = [
    'telephone' => $testTelephone,
    'code' => '123456', // Code fictif - le test échouera normalement si aucun code n'existe
];
$result = makeRequest('POST', "$baseUrl/auth/verify-phone", $verifyPhoneData);
recordTest(
    'Vérification téléphone',
    'POST',
    '/api/auth/verify-phone',
    $verifyPhoneData,
    $result,
    'Vérifier un numéro de téléphone avec le code reçu par SMS (test avec code fictif - nécessite un code réel pour réussir)'
);

echo "\n📝 Test 4b: Renvoyer le code de vérification\n";
$resendCodeData = [
    'telephone' => $testTelephone,
];
$result = makeRequest('POST', "$baseUrl/auth/resend-verification-code", $resendCodeData);
recordTest(
    'Renvoyer code vérification',
    'POST',
    '/api/auth/resend-verification-code',
    $resendCodeData,
    $result,
    'Renvoyer un nouveau code de vérification par SMS'
);

// ============================================
// 2. TESTS DU MENU (Routes publiques)
// ============================================

echo "\n📝 Test 5: Liste des catégories\n";
$result = makeRequest('GET', "$baseUrl/menu/categories");
recordTest(
    'Liste catégories',
    'GET',
    '/api/menu/categories',
    null,
    $result,
    'Obtenir toutes les catégories disponibles'
);

echo "\n📝 Test 6: Liste des catégories avec filtre qualité\n";
$result = makeRequest('GET', "$baseUrl/menu/categories?qualite=premium");
recordTest(
    'Liste catégories (filtre qualité)',
    'GET',
    '/api/menu/categories?qualite=premium',
    null,
    $result,
    'Obtenir les catégories avec filtre qualité premium'
);

echo "\n📝 Test 7: Détails d'une catégorie\n";
$result = makeRequest('GET', "$baseUrl/menu/categories/1");
recordTest(
    'Détails catégorie',
    'GET',
    '/api/menu/categories/1',
    null,
    $result,
    'Obtenir les détails d\'une catégorie spécifique'
);

echo "\n📝 Test 8: Liste des plats\n";
$result = makeRequest('GET', "$baseUrl/menu/plats");
recordTest(
    'Liste plats',
    'GET',
    '/api/menu/plats',
    null,
    $result,
    'Obtenir tous les plats disponibles'
);

echo "\n📝 Test 9: Liste des plats avec filtres\n";
$result = makeRequest('GET', "$baseUrl/menu/plats?categorie_id=1&qualite=premium&sort_by=prix&sort_order=asc");
recordTest(
    'Liste plats (avec filtres)',
    'GET',
    '/api/menu/plats?categorie_id=1&qualite=premium&sort_by=prix&sort_order=asc',
    null,
    $result,
    'Obtenir les plats avec filtres (catégorie, qualité, tri)'
);

echo "\n📝 Test 10: Recherche de plats\n";
$result = makeRequest('GET', "$baseUrl/menu/plats?search=riz");
recordTest(
    'Recherche plats',
    'GET',
    '/api/menu/plats?search=riz',
    null,
    $result,
    'Rechercher des plats par nom'
);

echo "\n📝 Test 11: Plats par catégorie\n";
$result = makeRequest('GET', "$baseUrl/menu/plats/category/1");
recordTest(
    'Plats par catégorie',
    'GET',
    '/api/menu/plats/category/1',
    null,
    $result,
    'Obtenir tous les plats d\'une catégorie spécifique'
);

echo "\n📝 Test 12: Détails d'un plat\n";
$result = makeRequest('GET', "$baseUrl/menu/plats/1");
recordTest(
    'Détails plat',
    'GET',
    '/api/menu/plats/1',
    null,
    $result,
    'Obtenir les détails complets d\'un plat'
);

// ============================================
// 3. TESTS AUTHENTIFIÉS (nécessitent un token)
// ============================================

if (!$authToken) {
    echo "\n⚠️  Aucun token d'authentification disponible. Les tests authentifiés seront ignorés.\n";
} else {
    $authHeaders = ["Authorization: Bearer $authToken"];
    
    echo "\n📝 Test 13: Informations utilisateur (authentifié)\n";
    $result = makeRequest('GET', "$baseUrl/auth/me", null, $authHeaders);
    recordTest(
        'Informations utilisateur',
        'GET',
        '/api/auth/me',
        null,
        $result,
        'Obtenir les informations de l\'utilisateur connecté'
    );
    
    echo "\n📝 Test 14: Mise à jour du profil\n";
    $updateData = [
        'name' => 'Test User API Modifié',
        'email' => 'testapi@example.com',
    ];
    $result = makeRequest('PUT', "$baseUrl/auth/profile", $updateData, $authHeaders);
    recordTest(
        'Mise à jour profil',
        'PUT',
        '/api/auth/profile',
        $updateData,
        $result,
        'Modifier les informations du profil utilisateur'
    );
    
    echo "\n📝 Test 15: Changer le mot de passe\n";
    $changePasswordData = [
        'current_password' => 'password123',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ];
    $result = makeRequest('POST', "$baseUrl/auth/change-password", $changePasswordData, $authHeaders);
    recordTest(
        'Changer mot de passe',
        'POST',
        '/api/auth/change-password',
        $changePasswordData,
        $result,
        'Modifier le mot de passe de l\'utilisateur'
    );
    
    // Remettre l'ancien mot de passe pour les tests suivants
    $resetPasswordData = [
        'current_password' => 'newpassword123',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];
    makeRequest('POST', "$baseUrl/auth/change-password", $resetPasswordData, $authHeaders);
    
    echo "\n📝 Test 15b: Rafraîchir le token\n";
    $result = makeRequest('POST', "$baseUrl/auth/refresh-token", null, $authHeaders);
    recordTest(
        'Rafraîchir token',
        'POST',
        '/api/auth/refresh-token',
        null,
        $result,
        'Rafraîchir le token d\'authentification et obtenir un nouveau token avec expiration d\'1 mois'
    );
    
    // Mettre à jour le token si le refresh a réussi
    if ($result['success']) {
        $responseData = json_decode($result['response'], true);
        if (isset($responseData['data']['access_token'])) {
            $authToken = $responseData['data']['access_token'];
            $authHeaders = ["Authorization: Bearer $authToken"];
            if (isset($responseData['data']['expires_at'])) {
                echo "✅ Nouveau token obtenu avec expiration: " . $responseData['data']['expires_at'] . "\n";
            }
        }
    }
    
    echo "\n📝 Test 16: Créer une commande\n";
    $commandeData = [
        'items' => [
            [
                'plat_id' => 1,
                'quantite' => 2,
                'accompagnements' => [],
                'options' => [],
            ],
        ],
        'montant_total' => 5000,
        'lieu' => 'Bureau principal',
        'lat' => 5.3600,
        'long' => -4.0083,
        'consigne_cuisinier' => 'Sans piment',
        'consigne_livreur' => 'Appeler avant d\'arriver',
        'mode_paiement' => 'mobile_money',
        'numero_telephone' => '+2250123456789',
    ];
    $result = makeRequest('POST', "$baseUrl/commandes", $commandeData, $authHeaders);
    recordTest(
        'Créer commande',
        'POST',
        '/api/commandes',
        $commandeData,
        $result,
        'Créer une nouvelle commande'
    );
    
    $commandeId = null;
    if ($result['success']) {
        $responseData = json_decode($result['response'], true);
        if (isset($responseData['data']['commande']['id'])) {
            $commandeId = $responseData['data']['commande']['id'];
        }
    }
    
    echo "\n📝 Test 17: Historique des commandes\n";
    $result = makeRequest('GET', "$baseUrl/commandes/history", null, $authHeaders);
    recordTest(
        'Historique commandes',
        'GET',
        '/api/commandes/history',
        null,
        $result,
        'Obtenir l\'historique des commandes de l\'utilisateur'
    );
    
    echo "\n📝 Test 18: Historique avec filtres\n";
    $result = makeRequest('GET', "$baseUrl/commandes/history?statut_commande=en_attente&per_page=10", null, $authHeaders);
    recordTest(
        'Historique commandes (avec filtres)',
        'GET',
        '/api/commandes/history?statut_commande=en_attente&per_page=10',
        null,
        $result,
        'Obtenir l\'historique avec filtres de statut'
    );
    
    if ($commandeId) {
        echo "\n📝 Test 19: Détails d'une commande\n";
        $result = makeRequest('GET', "$baseUrl/commandes/$commandeId", null, $authHeaders);
        recordTest(
            'Détails commande',
            'GET',
            "/api/commandes/$commandeId",
            null,
            $result,
            'Obtenir les détails d\'une commande spécifique'
        );
    }
    
    echo "\n📝 Test 20: Liste des favoris\n";
    $result = makeRequest('GET', "$baseUrl/favoris", null, $authHeaders);
    recordTest(
        'Liste favoris',
        'GET',
        '/api/favoris',
        null,
        $result,
        'Obtenir tous les plats favoris de l\'utilisateur'
    );
    
    echo "\n📝 Test 21: Ajouter un plat aux favoris\n";
    $favoriData = ['plat_id' => 1];
    $result = makeRequest('POST', "$baseUrl/favoris", $favoriData, $authHeaders);
    recordTest(
        'Ajouter favori',
        'POST',
        '/api/favoris',
        $favoriData,
        $result,
        'Ajouter un plat aux favoris'
    );
    
    echo "\n📝 Test 22: Vérifier si un plat est en favori\n";
    $result = makeRequest('GET', "$baseUrl/favoris/check/1", null, $authHeaders);
    recordTest(
        'Vérifier favori',
        'GET',
        '/api/favoris/check/1',
        null,
        $result,
        'Vérifier si un plat est dans les favoris'
    );
    
    echo "\n📝 Test 23: Retirer un plat des favoris\n";
    $result = makeRequest('DELETE', "$baseUrl/favoris/1", null, $authHeaders);
    recordTest(
        'Retirer favori',
        'DELETE',
        '/api/favoris/1',
        null,
        $result,
        'Retirer un plat des favoris'
    );
    
    echo "\n📝 Test 24: Déconnexion\n";
    $result = makeRequest('POST', "$baseUrl/auth/logout", null, $authHeaders);
    recordTest(
        'Déconnexion',
        'POST',
        '/api/auth/logout',
        null,
        $result,
        'Se déconnecter et supprimer le token'
    );
}

// ============================================
// GÉNÉRATION DU RAPPORT
// ============================================

echo "\n📊 Génération du rapport...\n";

$report = "# Rapport de Test API Domini\n\n";
$report .= "**Date de génération:** " . date('Y-m-d H:i:s') . "\n\n";
$report .= "**Base URL:** `$baseUrl`\n\n";

// Statistiques
$totalTests = count($results);
$successfulTests = count(array_filter($results, fn($r) => $r['success']));
$failedTests = $totalTests - $successfulTests;

$report .= "## 📊 Statistiques\n\n";
$report .= "- **Total des tests:** $totalTests\n";
$report .= "- **Tests réussis:** $successfulTests ✅\n";
$report .= "- **Tests échoués:** $failedTests ❌\n";
$report .= "- **Taux de réussite:** " . round(($successfulTests / $totalTests) * 100, 2) . "%\n\n";

$report .= "---\n\n";

// Détails de chaque test
$report .= "## 📝 Détails des Tests\n\n";

foreach ($results as $index => $test) {
    $testNum = $index + 1;
    $status = $test['success'] ? '✅' : '❌';
    
    $report .= "### Test $testNum: {$test['name']} $status\n\n";
    $report .= "**Endpoint:** `{$test['method']} {$test['endpoint']}`\n\n";
    $report .= "**Description:** {$test['description']}\n\n";
    
    if ($test['request_data']) {
        $report .= "**Données de requête:**\n\n";
        $report .= "```json\n";
        $report .= json_encode($test['request_data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $report .= "\n```\n\n";
    }
    
    $report .= "**Code de statut HTTP:** `{$test['status_code']}`\n\n";
    
    $report .= "**Réponse:**\n\n";
    $report .= "```json\n";
    $report .= $test['response'];
    $report .= "\n```\n\n";
    
    if ($test['error']) {
        $report .= "**Erreur:** `{$test['error']}`\n\n";
    }
    
    $report .= "**Timestamp:** {$test['timestamp']}\n\n";
    $report .= "---\n\n";
}

// Résumé par catégorie
$report .= "## 📋 Résumé par Catégorie\n\n";

$categories = [
    'Authentification' => ['Inscription', 'Connexion', 'Mot de passe', 'Vérification', 'Informations utilisateur', 'Mise à jour', 'Changer', 'Déconnexion'],
    'Menu' => ['catégories', 'plats', 'Détails'],
    'Commandes' => ['commande', 'Historique'],
    'Favoris' => ['favori'],
];

foreach ($categories as $category => $keywords) {
    $categoryTests = array_filter($results, function($test) use ($keywords) {
        foreach ($keywords as $keyword) {
            if (stripos($test['name'], $keyword) !== false) {
                return true;
            }
        }
        return false;
    });
    
    $categorySuccess = count(array_filter($categoryTests, fn($t) => $t['success']));
    $categoryTotal = count($categoryTests);
    
    $report .= "### $category\n\n";
    $report .= "- **Tests:** $categoryTotal\n";
    $report .= "- **Réussis:** $categorySuccess ✅\n";
    $report .= "- **Échoués:** " . ($categoryTotal - $categorySuccess) . " ❌\n\n";
}

// Commandes cURL pour reproduction
$report .= "## 🔧 Commandes cURL pour Reproduction\n\n";
$report .= "### Authentification\n\n";
$report .= "```bash\n";
$report .= "# Inscription\n";
$report .= "curl -X POST $baseUrl/auth/register \\\n";
$report .= "  -H \"Content-Type: application/json\" \\\n";
$report .= "  -d '{\"name\":\"Test User\",\"email\":\"test@example.com\",\"telephone\":\"+2250123456789\",\"password\":\"password123\",\"password_confirmation\":\"password123\"}'\n\n";
$report .= "# Connexion\n";
$report .= "curl -X POST $baseUrl/auth/login \\\n";
$report .= "  -H \"Content-Type: application/json\" \\\n";
$report .= "  -d '{\"email\":\"test@example.com\",\"password\":\"password123\"}'\n\n";
$report .= "```\n\n";

$report .= "### Menu\n\n";
$report .= "```bash\n";
$report .= "# Liste des catégories\n";
$report .= "curl -X GET \"$baseUrl/menu/categories\"\n\n";
$report .= "# Liste des plats\n";
$report .= "curl -X GET \"$baseUrl/menu/plats\"\n\n";
$report .= "# Détails d'un plat\n";
$report .= "curl -X GET \"$baseUrl/menu/plats/1\"\n\n";
$report .= "```\n\n";

$report .= "### Commandes (nécessite authentification)\n\n";
$report .= "```bash\n";
$report .= "# Créer une commande\n";
$report .= "curl -X POST \"$baseUrl/commandes\" \\\n";
$report .= "  -H \"Authorization: Bearer VOTRE_TOKEN\" \\\n";
$report .= "  -H \"Content-Type: application/json\" \\\n";
$report .= "  -d '{\"items\":[{\"plat_id\":1,\"quantite\":2}],\"montant_total\":5000,\"mode_paiement\":\"mobile_money\"}'\n\n";
$report .= "```\n\n";

// Sauvegarder le rapport
$reportFile = __DIR__ . '/RAPPORT_TEST_API.md';
file_put_contents($reportFile, $report);

echo "✅ Rapport généré: $reportFile\n";
echo "\n📊 Résumé:\n";
echo "   - Total: $totalTests tests\n";
echo "   - Réussis: $successfulTests ✅\n";
echo "   - Échoués: $failedTests ❌\n";
echo "   - Taux de réussite: " . round(($successfulTests / $totalTests) * 100, 2) . "%\n";
