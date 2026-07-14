# Test simplifié des API d'authentification SMS
$ErrorActionPreference = "Continue"

$baseUrl = "http://localhost:8000/api"
$phoneNumber = "2250748526787"

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "  TEST API AUTH - ENVOI DE SMS" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Numéro de test: $phoneNumber" -ForegroundColor Yellow
Write-Host ""

# Nettoyer le cache
Write-Host "Nettoyage du cache..." -ForegroundColor Gray
php artisan config:clear 2>&1 | Out-Null
php artisan cache:clear 2>&1 | Out-Null
Write-Host "Cache nettoyé`n" -ForegroundColor Green

# ============================================
# TEST 1 : Inscription
# ============================================
Write-Host "TEST 1 : Inscription (avec envoi SMS)" -ForegroundColor Green
Write-Host "--------------------------------------" -ForegroundColor Gray
Write-Host ""

$randomEmail = "test$(Get-Random -Minimum 10000 -Maximum 99999)@domini.test"
$randomName = "TestUser$(Get-Random -Minimum 1000 -Maximum 9999)"

Write-Host "Email: $randomEmail" -ForegroundColor White
Write-Host "Nom: $randomName" -ForegroundColor White
Write-Host "Tel: $phoneNumber`n" -ForegroundColor White

$registerBody = @{
    name = $randomName
    email = $randomEmail
    telephone = $phoneNumber
    password = "Test123456"
    password_confirmation = "Test123456"
    role = "employe"
} | ConvertTo-Json

Write-Host "Envoi de la requête..." -ForegroundColor Yellow

$registerResponse = Invoke-RestMethod -Uri "$baseUrl/auth/register" `
    -Method POST `
    -ContentType "application/json" `
    -Body $registerBody `
    -ErrorAction SilentlyContinue

Write-Host ""
if ($registerResponse -and $registerResponse.success) {
    Write-Host "INSCRIPTION REUSSIE !" -ForegroundColor Green
    Write-Host "SMS envoyé: $($registerResponse.sms_sent)" -ForegroundColor $(if($registerResponse.sms_sent){"Green"}else{"Red"})
    Write-Host "Message SMS: $($registerResponse.sms_message)" -ForegroundColor White
    Write-Host "User ID: $($registerResponse.data.user.id)`n" -ForegroundColor White
    
    $global:testPhone = $phoneNumber
} else {
    Write-Host "ECHEC de l'inscription" -ForegroundColor Red
    if ($registerResponse) {
        Write-Host "Message: $($registerResponse.message)`n" -ForegroundColor Yellow
    }
}

Start-Sleep -Seconds 2

# ============================================
# TEST 2 : Renvoyer le code
# ============================================
Write-Host "TEST 2 : Renvoyer le code de vérification" -ForegroundColor Green
Write-Host "------------------------------------------" -ForegroundColor Gray
Write-Host ""

$resendBody = @{
    telephone = $phoneNumber
} | ConvertTo-Json

Write-Host "Envoi de la requête..." -ForegroundColor Yellow

$resendResponse = Invoke-RestMethod -Uri "$baseUrl/auth/resend-verification-code" `
    -Method POST `
    -ContentType "application/json" `
    -Body $resendBody `
    -ErrorAction SilentlyContinue

Write-Host ""
if ($resendResponse -and $resendResponse.success) {
    Write-Host "CODE RENVOYE AVEC SUCCES !" -ForegroundColor Green
    Write-Host "Message: $($resendResponse.message)`n" -ForegroundColor White
} else {
    Write-Host "ECHEC du renvoi" -ForegroundColor Red
    if ($resendResponse) {
        Write-Host "Message: $($resendResponse.message)`n" -ForegroundColor Yellow
    }
}

Start-Sleep -Seconds 2

# ============================================
# TEST 3 : Mot de passe oublié
# ============================================
Write-Host "TEST 3 : Mot de passe oublié (envoi SMS)" -ForegroundColor Green
Write-Host "------------------------------------------" -ForegroundColor Gray
Write-Host ""

$forgotBody = @{
    telephone = $phoneNumber
} | ConvertTo-Json

Write-Host "Envoi de la requête..." -ForegroundColor Yellow

$forgotResponse = Invoke-RestMethod -Uri "$baseUrl/auth/forgot-password" `
    -Method POST `
    -ContentType "application/json" `
    -Body $forgotBody `
    -ErrorAction SilentlyContinue

Write-Host ""
if ($forgotResponse -and $forgotResponse.success) {
    Write-Host "CODE DE REINITIALISATION ENVOYE !" -ForegroundColor Green
    Write-Host "Message: $($forgotResponse.message)`n" -ForegroundColor White
} else {
    Write-Host "ECHEC de l'envoi" -ForegroundColor Red
    if ($forgotResponse) {
        Write-Host "Message: $($forgotResponse.message)`n" -ForegroundColor Yellow
    }
}

# ============================================
# Logs
# ============================================
Write-Host "LOGS ORANGE SMS (10 dernières lignes)" -ForegroundColor Green
Write-Host "--------------------------------------" -ForegroundColor Gray
Write-Host ""

$logs = Get-Content storage/logs/laravel.log -Tail 100 | Select-String "Orange SMS" | Select-Object -Last 10
if ($logs) {
    $logs | ForEach-Object {
        if ($_ -match "succès") {
            Write-Host "OK  " -NoNewline -ForegroundColor Green
        } elseif ($_ -match "ERROR") {
            Write-Host "ERR " -NoNewline -ForegroundColor Red
        } else {
            Write-Host "LOG " -NoNewline -ForegroundColor Blue
        }
        Write-Host $_ -ForegroundColor White
    }
} else {
    Write-Host "Aucun log SMS trouvé" -ForegroundColor Gray
}

Write-Host ""
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "Tests terminés - Vérifiez votre téléphone" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""
