# Test d'envoi de SMS au numéro 0748526787
# Date: 2026-02-12

$baseUrl = "http://localhost:8000"
$phoneNumber = "2250576009958"  # Format international
$message = "Test SMS depuis OrangeSmsService - " + (Get-Date -Format "HH:mm:ss")

Write-Host "=======================================" -ForegroundColor Cyan
Write-Host "TEST ENVOI SMS ORANGE API" -ForegroundColor Cyan
Write-Host "=======================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Numéro destinataire: $phoneNumber" -ForegroundColor Yellow
Write-Host "Message: $message" -ForegroundColor Yellow
Write-Host ""

# Créer le payload JSON
$body = @{
    phone = $phoneNumber
    message = $message
} | ConvertTo-Json

Write-Host "Envoi de la requête..." -ForegroundColor Green
Write-Host ""

try {
    # Effectuer la requête
    $response = Invoke-RestMethod -Uri "$baseUrl/api/test-sms" -Method Post -Body $body -ContentType "application/json" -ErrorAction Stop
    
    Write-Host "=======================================" -ForegroundColor Green
    Write-Host "RÉPONSE DE L'API" -ForegroundColor Green
    Write-Host "=======================================" -ForegroundColor Green
    Write-Host ""
    
    $response | ConvertTo-Json -Depth 10 | Write-Host
    
    Write-Host ""
    if ($response.success -eq $true) {
        Write-Host "SMS ENVOYE AVEC SUCCES!" -ForegroundColor Green
    } else {
        Write-Host "ECHEC DE L'ENVOI" -ForegroundColor Red
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
        $_.ErrorDetails.Message | Write-Host
    }
}

Write-Host ""
Write-Host "=======================================" -ForegroundColor Cyan
Write-Host "Vérifiez les logs Laravel pour plus de détails:" -ForegroundColor Cyan
Write-Host "storage/logs/laravel.log" -ForegroundColor Yellow
Write-Host "=======================================" -ForegroundColor Cyan
