# Test d'inscription complète avec envoi de SMS
# Date: 2026-02-12

$baseUrl = "http://localhost:8000"

Write-Host "=======================================" -ForegroundColor Cyan
Write-Host "TEST INSCRIPTION AVEC CODE SMS" -ForegroundColor Cyan
Write-Host "=======================================" -ForegroundColor Cyan
Write-Host ""

# Données d'inscription
$body = @{
    name = "Test User SMS"
    email = "test.sms.$(Get-Date -Format 'HHmmss')@domini.com"
    telephone = "0748526787"
    password = "password123"
    password_confirmation = "password123"
    role = "employe"
} | ConvertTo-Json

Write-Host "Données d'inscription:" -ForegroundColor Yellow
Write-Host "Nom: Test User SMS"
Write-Host "Email: $($body | ConvertFrom-Json | Select-Object -ExpandProperty email)"
Write-Host "Téléphone: 0748526787"
Write-Host ""

Write-Host "Envoi de la requête d'inscription..." -ForegroundColor Green
Write-Host ""

try {
    $response = Invoke-RestMethod -Uri "$baseUrl/api/auth/register" -Method Post -Body $body -ContentType "application/json" -ErrorAction Stop
    
    Write-Host "=======================================" -ForegroundColor Green
    Write-Host "RÉPONSE DE L'API" -ForegroundColor Green
    Write-Host "=======================================" -ForegroundColor Green
    Write-Host ""
    
    $response | ConvertTo-Json -Depth 10 | Write-Host
    
    Write-Host ""
    if ($response.success -eq $true) {
        Write-Host "INSCRIPTION REUSSIE!" -ForegroundColor Green
        if ($response.sms_sent -eq $true) {
            Write-Host "SMS ENVOYE AVEC SUCCES!" -ForegroundColor Green
        } else {
            Write-Host "ATTENTION: SMS NON ENVOYE" -ForegroundColor Red
            Write-Host "Message: $($response.sms_message)" -ForegroundColor Yellow
        }
    } else {
        Write-Host "ECHEC DE L'INSCRIPTION" -ForegroundColor Red
    }
    
} catch {
    Write-Host "=======================================" -ForegroundColor Red
    Write-Host "ERREUR" -ForegroundColor Red
    Write-Host "=======================================" -ForegroundColor Red
    Write-Host ""
    Write-Host $_.Exception.Message -ForegroundColor Red
    
    if ($_.ErrorDetails.Message) {
        Write-Host ""
        Write-Host "Détails:" -ForegroundColor Yellow
        try {
            $errorDetails = $_.ErrorDetails.Message | ConvertFrom-Json
            $errorDetails | ConvertTo-Json -Depth 10 | Write-Host
        } catch {
            $_.ErrorDetails.Message | Write-Host
        }
    }
}

Write-Host ""
Write-Host "=======================================" -ForegroundColor Cyan
Write-Host "Vérifiez:" -ForegroundColor Cyan
Write-Host "1. Le SMS sur le téléphone 0748526787" -ForegroundColor Yellow
Write-Host "2. Les logs Laravel: storage/logs/laravel.log" -ForegroundColor Yellow
Write-Host "=======================================" -ForegroundColor Cyan
