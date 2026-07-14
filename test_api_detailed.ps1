# Test détaillé des routes API SMS avec capture complète des erreurs
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Test Routes API SMS (Détaillé)" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$baseUrl = "http://localhost:8000/api"

# Test avec le numéro spécifié
$phone = "22505760099558"
Write-Host "[TEST] resendVerificationCode avec $phone" -ForegroundColor Yellow
Write-Host ""

$body = @{
    telephone = $phone
} | ConvertTo-Json

try {
    $response = Invoke-WebRequest -Uri "$baseUrl/auth/resend-verification-code" `
        -Method Post `
        -Headers @{
            "Content-Type" = "application/json"
            "Accept" = "application/json"
        } `
        -Body $body `
        -UseBasicParsing
    
    Write-Host "✓ SUCCES (HTTP $($response.StatusCode))!" -ForegroundColor Green
    $response.Content | ConvertFrom-Json | ConvertTo-Json -Depth 5 | Write-Host -ForegroundColor Gray
} catch {
    $statusCode = $_.Exception.Response.StatusCode.value__
    Write-Host "✗ ERREUR HTTP $statusCode" -ForegroundColor Red
    
    # Lire le body de l'erreur
    $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
    $reader.BaseStream.Position = 0
    $reader.DiscardBufferedData()
    $responseBody = $reader.ReadToEnd()
    
    Write-Host "Réponse complète:" -ForegroundColor Yellow
    Write-Host $responseBody -ForegroundColor Red
    
    try {
        $errorJson = $responseBody | ConvertFrom-Json
        Write-Host ""
        Write-Host "JSON parsé:" -ForegroundColor Yellow
        Write-Host ($errorJson | ConvertTo-Json -Depth 5) -ForegroundColor Red
    } catch {
        Write-Host "(Impossible de parser le JSON)" -ForegroundColor Gray
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Test avec format local" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$phoneLocal = "0748526787"
Write-Host "[TEST] resendVerificationCode avec $phoneLocal" -ForegroundColor Yellow
Write-Host ""

$bodyLocal = @{
    telephone = $phoneLocal
} | ConvertTo-Json

try {
    $response = Invoke-WebRequest -Uri "$baseUrl/auth/resend-verification-code" `
        -Method Post `
        -Headers @{
            "Content-Type" = "application/json"
            "Accept" = "application/json"
        } `
        -Body $bodyLocal `
        -UseBasicParsing
    
    Write-Host "✓ SUCCES (HTTP $($response.StatusCode))!" -ForegroundColor Green
    $response.Content | ConvertFrom-Json | ConvertTo-Json -Depth 5 | Write-Host -ForegroundColor Gray
} catch {
    $statusCode = $_.Exception.Response.StatusCode.value__
    Write-Host "✗ ERREUR HTTP $statusCode" -ForegroundColor Red
    
    # Lire le body de l'erreur
    $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
    $reader.BaseStream.Position = 0
    $reader.DiscardBufferedData()
    $responseBody = $reader.ReadToEnd()
    
    Write-Host "Réponse complète:" -ForegroundColor Yellow
    Write-Host $responseBody -ForegroundColor Red
    
    try {
        $errorJson = $responseBody | ConvertFrom-Json
        Write-Host ""
        Write-Host "JSON parsé:" -ForegroundColor Yellow
        Write-Host ($errorJson | ConvertTo-Json -Depth 5) -ForegroundColor Red
    } catch {
        Write-Host "(Impossible de parser le JSON)" -ForegroundColor Gray
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Tests terminés" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
