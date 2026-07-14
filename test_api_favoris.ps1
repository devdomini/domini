# Script PowerShell pour tester les API Favoris
# Date: 12 février 2026

Write-Host "🧪 Test des API Favoris" -ForegroundColor Cyan
Write-Host "=" -ForegroundColor Cyan

$baseUrl = "http://127.0.0.1:8000/api"

# Étape 1: Login pour obtenir un token
Write-Host "`n1️⃣ Connexion..." -ForegroundColor Yellow
$loginData = @{
    telephone = "2250748526787"
    mot_de_passe = "password123"
} | ConvertTo-Json

try {
    $loginResponse = Invoke-RestMethod -Uri "$baseUrl/auth/login" -Method Post -Body $loginData -ContentType "application/json"
    $token = $loginResponse.data.token
    Write-Host "✅ Connexion réussie!" -ForegroundColor Green
    Write-Host "Token: $($token.Substring(0, 20))..." -ForegroundColor Gray
} catch {
    Write-Host "❌ Erreur de connexion: $_" -ForegroundColor Red
    exit 1
}

$headers = @{
    "Authorization" = "Bearer $token"
    "Content-Type" = "application/json"
    "Accept" = "application/json"
}

# Étape 2: Récupérer un plat (pour avoir un ID)
Write-Host "`n2️⃣ Récupération d'un plat..." -ForegroundColor Yellow
try {
    $platsResponse = Invoke-RestMethod -Uri "$baseUrl/menu/plats" -Method Get -Headers $headers
    $platId = $platsResponse.plats[0].id
    $platNom = $platsResponse.plats[0].nom
    Write-Host "✅ Plat récupéré: ID=$platId, Nom=$platNom" -ForegroundColor Green
} catch {
    Write-Host "❌ Erreur récupération plat: $_" -ForegroundColor Red
    exit 1
}

# Étape 3: Lister les favoris (initialement vide ou avec des données)
Write-Host "`n3️⃣ Liste des favoris actuels..." -ForegroundColor Yellow
try {
    $favorisResponse = Invoke-RestMethod -Uri "$baseUrl/favoris" -Method Get -Headers $headers
    Write-Host "✅ Favoris actuels: $($favorisResponse.data.total)" -ForegroundColor Green
    if ($favorisResponse.data.total -gt 0) {
        Write-Host "📋 Liste:" -ForegroundColor Gray
        foreach ($favori in $favorisResponse.data.favoris) {
            Write-Host "   - ID=$($favori.id), Plat=$($favori.plat.nom)" -ForegroundColor Gray
        }
    }
} catch {
    Write-Host "❌ Erreur liste favoris: $_" -ForegroundColor Red
}

# Étape 4: Vérifier si le plat est en favori
Write-Host "`n4️⃣ Vérification si le plat $platId est en favori..." -ForegroundColor Yellow
try {
    $checkResponse = Invoke-RestMethod -Uri "$baseUrl/favoris/check/$platId" -Method Get -Headers $headers
    $isFavori = $checkResponse.data.is_favori
    Write-Host "✅ Le plat est en favori: $isFavori" -ForegroundColor Green
    
    # Si déjà en favori, on le retire d'abord
    if ($isFavori) {
        Write-Host "`n5️⃣ Le plat est déjà en favori, on le retire..." -ForegroundColor Yellow
        try {
            $removeResponse = Invoke-RestMethod -Uri "$baseUrl/favoris/$platId" -Method Delete -Headers $headers
            Write-Host "✅ $($removeResponse.message)" -ForegroundColor Green
        } catch {
            Write-Host "❌ Erreur retrait: $_" -ForegroundColor Red
        }
    }
} catch {
    Write-Host "❌ Erreur vérification: $_" -ForegroundColor Red
}

# Étape 5: Ajouter le plat aux favoris
Write-Host "`n6️⃣ Ajout du plat $platId ($platNom) aux favoris..." -ForegroundColor Yellow
$addData = @{
    plat_id = $platId
} | ConvertTo-Json

try {
    $addResponse = Invoke-RestMethod -Uri "$baseUrl/favoris" -Method Post -Body $addData -Headers $headers
    Write-Host "✅ $($addResponse.message)" -ForegroundColor Green
    Write-Host "📋 Favori créé:" -ForegroundColor Gray
    Write-Host "   - ID: $($addResponse.data.favori.id)" -ForegroundColor Gray
    Write-Host "   - Plat: $($addResponse.data.favori.plat.nom)" -ForegroundColor Gray
    Write-Host "   - Prix: $($addResponse.data.favori.plat.prix) FCFA" -ForegroundColor Gray
} catch {
    Write-Host "❌ Erreur ajout: $_" -ForegroundColor Red
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $responseBody = $reader.ReadToEnd()
        Write-Host "Détails: $responseBody" -ForegroundColor Red
    }
}

# Étape 6: Vérifier à nouveau
Write-Host "`n7️⃣ Vérification après ajout..." -ForegroundColor Yellow
try {
    $checkResponse2 = Invoke-RestMethod -Uri "$baseUrl/favoris/check/$platId" -Method Get -Headers $headers
    Write-Host "✅ Le plat est en favori: $($checkResponse2.data.is_favori)" -ForegroundColor Green
} catch {
    Write-Host "❌ Erreur vérification: $_" -ForegroundColor Red
}

# Étape 7: Lister les favoris après ajout
Write-Host "`n8️⃣ Liste des favoris après ajout..." -ForegroundColor Yellow
try {
    $favorisResponse2 = Invoke-RestMethod -Uri "$baseUrl/favoris" -Method Get -Headers $headers
    Write-Host "✅ Total favoris: $($favorisResponse2.data.total)" -ForegroundColor Green
    Write-Host "📋 Liste:" -ForegroundColor Gray
    foreach ($favori in $favorisResponse2.data.favoris) {
        Write-Host "   - ID=$($favori.id), Plat=$($favori.plat.nom), Prix=$($favori.plat.prix) FCFA" -ForegroundColor Gray
    }
} catch {
    Write-Host "❌ Erreur liste favoris: $_" -ForegroundColor Red
}

# Étape 8: Retirer le plat des favoris
Write-Host "`n9️⃣ Retrait du plat $platId des favoris..." -ForegroundColor Yellow
try {
    $removeResponse2 = Invoke-RestMethod -Uri "$baseUrl/favoris/$platId" -Method Delete -Headers $headers
    Write-Host "✅ $($removeResponse2.message)" -ForegroundColor Green
} catch {
    Write-Host "❌ Erreur retrait: $_" -ForegroundColor Red
}

# Étape 9: Vérification finale
Write-Host "`n🔟 Vérification finale..." -ForegroundColor Yellow
try {
    $checkResponse3 = Invoke-RestMethod -Uri "$baseUrl/favoris/check/$platId" -Method Get -Headers $headers
    Write-Host "✅ Le plat est en favori: $($checkResponse3.data.is_favori)" -ForegroundColor Green
} catch {
    Write-Host "❌ Erreur vérification: $_" -ForegroundColor Red
}

Write-Host "`n✅ Tests terminés!" -ForegroundColor Cyan
Write-Host "=" -ForegroundColor Cyan
