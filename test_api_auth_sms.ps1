# Test des API d'authentification qui envoient des SMS
# Date: 2026-02-12

$baseUrl = "http://localhost:8000/api"
$phoneNumber = "2250748526787"

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "  TEST API AUTH - ENVOI DE SMS" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "📱 Numéro de test: " -NoNewline -ForegroundColor Yellow
Write-Host $phoneNumber -ForegroundColor White
Write-Host "🌐 Base URL: " -NoNewline -ForegroundColor Yellow
Write-Host $baseUrl -ForegroundColor White
Write-Host ""

# Nettoyer le cache
Write-Host "Nettoyage du cache..." -ForegroundColor Gray
php artisan config:clear 2>&1 | Out-Null
php artisan cache:clear 2>&1 | Out-Null
Write-Host "✓ Cache nettoyé" -ForegroundColor Green
Write-Host ""

# ============================================
# TEST 1 : API Inscription (envoie un SMS)
# ============================================
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Gray
Write-Host "TEST 1 : Inscription (POST /auth/register)" -ForegroundColor Green
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Gray
Write-Host ""

$randomEmail = "test" + (Get-Random -Minimum 10000 -Maximum 99999) + "@domini.test"
$randomName = "Test User " + (Get-Random -Minimum 1000 -Maximum 9999)

Write-Host "Données d'inscription :" -ForegroundColor Yellow
Write-Host "  Nom       : $randomName" -ForegroundColor White
Write-Host "  Email     : $randomEmail" -ForegroundColor White
Write-Host "  Téléphone : $phoneNumber" -ForegroundColor White
Write-Host ""

$registerBody = @{
    name = $randomName
    email = $randomEmail
    telephone = $phoneNumber
    password = "Test123456"
    password_confirmation = "Test123456"
    role = "employe"
} | ConvertTo-Json

Write-Host "📤 Envoi de la requête d'inscription..." -ForegroundColor Yellow

try {
    $registerResponse = Invoke-RestMethod -Uri "$baseUrl/auth/register" `
        -Method POST `
        -ContentType "application/json" `
        -Body $registerBody `
        -ErrorAction Stop
    
    Write-Host ""
    if ($registerResponse.success) {
        Write-Host "✅ INSCRIPTION RÉUSSIE !" -ForegroundColor Green
        Write-Host ""
        Write-Host "Informations de réponse :" -ForegroundColor Cyan
        Write-Host "  Message       : $($registerResponse.message)" -ForegroundColor White
        Write-Host "  SMS envoyé    : " -NoNewline -ForegroundColor White
        if ($registerResponse.sms_sent) {
            Write-Host "OUI ✓" -ForegroundColor Green
        } else {
            Write-Host "NON ✗" -ForegroundColor Red
        }
        Write-Host "  Message SMS   : $($registerResponse.sms_message)" -ForegroundColor White
        Write-Host "  User ID       : $($registerResponse.data.user.id)" -ForegroundColor White
        Write-Host "  Téléphone     : $($registerResponse.data.user.telephone)" -ForegroundColor White
        Write-Host "  Vérifié       : $($registerResponse.data.user.telephone_verified_at)" -ForegroundColor White
        Write-Host ""
        
        # Sauvegarder le token et les infos pour les tests suivants
        $global:accessToken = $registerResponse.data.access_token
        $global:userId = $registerResponse.data.user.id
        
        if ($registerResponse.sms_sent) {
            Write-Host "✉️  SMS ENVOYÉ ! Vérifiez votre téléphone pour le code." -ForegroundColor Green
        } else {
            Write-Host "⚠️  ATTENTION : Le SMS n'a pas été envoyé !" -ForegroundColor Yellow
        }
    } else {
        Write-Host "❌ ÉCHEC DE L'INSCRIPTION" -ForegroundColor Red
        Write-Host "  Message : $($registerResponse.message)" -ForegroundColor Yellow
        if ($registerResponse.errors) {
            Write-Host "  Erreurs :" -ForegroundColor Yellow
            $registerResponse.errors.PSObject.Properties | ForEach-Object {
                Write-Host "    - $($_.Name): $($_.Value)" -ForegroundColor White
            }
        }
    }
} catch {
    Write-Host "❌ ERREUR HTTP" -ForegroundColor Red
    Write-Host "  Message : $($_.Exception.Message)" -ForegroundColor Yellow
    
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $errorBody = $reader.ReadToEnd()
        Write-Host "  Réponse : $errorBody" -ForegroundColor Gray
    }
}

Write-Host ""
Write-Host "Pause de 3 secondes avant le test suivant..." -ForegroundColor Gray
Start-Sleep -Seconds 3
Write-Host ""

# ============================================
# TEST 2 : Renvoyer le code de vérification
# ============================================
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Gray
Write-Host "TEST 2 : Renvoyer code (POST /auth/resend-verification-code)" -ForegroundColor Green
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Gray
Write-Host ""

$resendBody = @{
    telephone = $phoneNumber
} | ConvertTo-Json

Write-Host "📤 Envoi de la requête de renvoi du code..." -ForegroundColor Yellow

try {
    $resendResponse = Invoke-RestMethod -Uri "$baseUrl/auth/resend-verification-code" `
        -Method POST `
        -ContentType "application/json" `
        -Body $resendBody `
        -ErrorAction Stop
    
    Write-Host ""
    if ($resendResponse.success) {
        Write-Host "✅ CODE RENVOYÉ AVEC SUCCÈS !" -ForegroundColor Green
        Write-Host "  Message : $($resendResponse.message)" -ForegroundColor White
        Write-Host ""
        Write-Host "✉️  Nouveau SMS envoyé ! Vérifiez votre téléphone." -ForegroundColor Green
    } else {
        Write-Host "❌ ÉCHEC DU RENVOI" -ForegroundColor Red
        Write-Host "  Message : $($resendResponse.message)" -ForegroundColor Yellow
    }
} catch {
    Write-Host "❌ ERREUR HTTP" -ForegroundColor Red
    Write-Host "  Message : $($_.Exception.Message)" -ForegroundColor Yellow
    
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $errorBody = $reader.ReadToEnd()
        Write-Host "  Réponse : $errorBody" -ForegroundColor Gray
    }
}

Write-Host ""
Write-Host "Pause de 3 secondes avant le test suivant..." -ForegroundColor Gray
Start-Sleep -Seconds 3
Write-Host ""

# ============================================
# TEST 3 : Mot de passe oublié (envoie un SMS)
# ============================================
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Gray
Write-Host "TEST 3 : Mot de passe oublié (POST /auth/forgot-password)" -ForegroundColor Green
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Gray
Write-Host ""

$forgotBody = @{
    telephone = $phoneNumber
} | ConvertTo-Json

Write-Host "📤 Envoi de la requête mot de passe oublié..." -ForegroundColor Yellow

try {
    $forgotResponse = Invoke-RestMethod -Uri "$baseUrl/auth/forgot-password" `
        -Method POST `
        -ContentType "application/json" `
        -Body $forgotBody `
        -ErrorAction Stop
    
    Write-Host ""
    if ($forgotResponse.success) {
        Write-Host "✅ CODE DE RÉINITIALISATION ENVOYÉ !" -ForegroundColor Green
        Write-Host "  Message : $($forgotResponse.message)" -ForegroundColor White
        Write-Host ""
        Write-Host "✉️  SMS de réinitialisation envoyé ! Vérifiez votre téléphone." -ForegroundColor Green
    } else {
        Write-Host "❌ ÉCHEC DE L'ENVOI" -ForegroundColor Red
        Write-Host "  Message : $($forgotResponse.message)" -ForegroundColor Yellow
    }
} catch {
    Write-Host "❌ ERREUR HTTP" -ForegroundColor Red
    Write-Host "  Message : $($_.Exception.Message)" -ForegroundColor Yellow
    
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $errorBody = $reader.ReadToEnd()
        Write-Host "  Réponse : $errorBody" -ForegroundColor Gray
    }
}

Write-Host ""

# ============================================
# Vérification des logs
# ============================================
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Gray
Write-Host "VÉRIFICATION DES LOGS LARAVEL" -ForegroundColor Green
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Gray
Write-Host ""

Write-Host "Dernières entrées SMS dans les logs :" -ForegroundColor Yellow
Write-Host ""

$logs = Get-Content storage/logs/laravel.log -Tail 100 | Select-String "Orange SMS"
if ($logs) {
    $logs | Select-Object -Last 10 | ForEach-Object {
        $line = $_.ToString()
        if ($line -match "INFO.*envoyé avec succès") {
            Write-Host "  ✅ " -NoNewline -ForegroundColor Green
            Write-Host $line -ForegroundColor White
        } elseif ($line -match "ERROR") {
            Write-Host "  ❌ " -NoNewline -ForegroundColor Red
            Write-Host $line -ForegroundColor White
        } else {
            Write-Host "  ℹ️  " -NoNewline -ForegroundColor Blue
            Write-Host $line -ForegroundColor White
        }
    }
} else {
    Write-Host "  ℹ️  Aucune entrée SMS dans les logs récents" -ForegroundColor Gray
}

Write-Host ""

# ============================================
# RÉSUMÉ
# ============================================
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "  RÉSUMÉ DES TESTS" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "📊 Tests effectués :" -ForegroundColor Yellow
Write-Host "  1. ✓ Inscription avec envoi de code SMS" -ForegroundColor White
Write-Host "  2. ✓ Renvoi du code de vérification" -ForegroundColor White
Write-Host "  3. ✓ Mot de passe oublié avec envoi de code SMS" -ForegroundColor White
Write-Host ""

Write-Host "📱 Vérifiez votre téléphone : $phoneNumber" -ForegroundColor Cyan
Write-Host ""

Write-Host "💡 Conseils :" -ForegroundColor Yellow
Write-Host "  - Les SMS peuvent prendre quelques secondes à arriver" -ForegroundColor White
Write-Host "  - Vérifiez que le numéro est correct et actif" -ForegroundColor White
Write-Host "  - Consultez les logs ci-dessus pour voir si les SMS ont été envoyés" -ForegroundColor White
Write-Host ""

Write-Host "Pour voir les logs en temps réel :" -ForegroundColor Cyan
Write-Host "  Get-Content storage/logs/laravel.log -Tail 50 -Wait" -ForegroundColor White
Write-Host ""

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""
