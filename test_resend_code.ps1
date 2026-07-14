# Test Route resendVerificationCode
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Test Route resendVerificationCode" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$baseUrl = "http://localhost:8000/api"
$phoneWithCode = "225748526787"
$phoneLocal = "0748526787"

Write-Host "IMPORTANT: Le numero doit exister dans la base de donnees!" -ForegroundColor Yellow
Write-Host ""
Write-Host "Format attendu dans la BD: $phoneLocal (format local)" -ForegroundColor Gray
Write-Host "Format fourni: $phoneWithCode (format avec indicatif)" -ForegroundColor Gray
Write-Host ""

# Test avec format avec indicatif
Write-Host "[TEST 1] Avec format indicatif ($phoneWithCode)..." -ForegroundColor Yellow
Write-Host ""

$body1 = @{
    telephone = $phoneWithCode
} | ConvertTo-Json

try {
    $response1 = Invoke-RestMethod -Uri "$baseUrl/auth/resend-verification-code" `
        -Method Post `
        -Headers @{
            "Content-Type" = "application/json"
            "Accept" = "application/json"
        } `
        -Body $body1
    
    Write-Host "✓ Succes!" -ForegroundColor Green
    Write-Host ($response1 | ConvertTo-Json) -ForegroundColor Gray
} catch {
    Write-Host "✗ Erreur: $($_.Exception.Message)" -ForegroundColor Red
    if ($_.ErrorDetails.Message) {
        Write-Host "Details: $($_.ErrorDetails.Message)" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "[TEST 2] Avec format local ($phoneLocal)..." -ForegroundColor Yellow
Write-Host ""

$body2 = @{
    telephone = $phoneLocal
} | ConvertTo-Json

try {
    $response2 = Invoke-RestMethod -Uri "$baseUrl/auth/resend-verification-code" `
        -Method Post `
        -Headers @{
            "Content-Type" = "application/json"
            "Accept" = "application/json"
        } `
        -Body $body2
    
    Write-Host "✓ Succes!" -ForegroundColor Green
    Write-Host ($response2 | ConvertTo-Json) -ForegroundColor Gray
} catch {
    Write-Host "✗ Erreur: $($_.Exception.Message)" -ForegroundColor Red
    if ($_.ErrorDetails.Message) {
        Write-Host "Details: $($_.ErrorDetails.Message)" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Tests termines" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Verifiez le SMS sur le numero $phoneLocal" -ForegroundColor Yellow
Write-Host ""
