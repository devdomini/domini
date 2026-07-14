# Test complet des routes API SMS
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Test Routes API SMS" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$baseUrl = "http://localhost:8000/api"

# Liste des numéros à tester
$phones = @(
    "0748526787",
    "225748526787",
    "0576009958",
    "225576009958"
)

foreach ($phone in $phones) {
    Write-Host "[TEST] resendVerificationCode avec $phone" -ForegroundColor Yellow
    Write-Host ""
    
    $body = @{
        telephone = $phone
    } | ConvertTo-Json
    
    try {
        $response = Invoke-RestMethod -Uri "$baseUrl/auth/resend-verification-code" `
            -Method Post `
            -Headers @{
                "Content-Type" = "application/json"
                "Accept" = "application/json"
            } `
            -Body $body
        
        Write-Host "✓ SUCCES!" -ForegroundColor Green
        Write-Host ($response | ConvertTo-Json -Depth 5) -ForegroundColor Gray
        Write-Host ""
    } catch {
        $statusCode = $_.Exception.Response.StatusCode.value__
        $errorMessage = $_.ErrorDetails.Message
        
        Write-Host "✗ ERREUR $statusCode" -ForegroundColor Red
        
        if ($errorMessage) {
            try {
                $errorJson = $errorMessage | ConvertFrom-Json
                Write-Host ($errorJson | ConvertTo-Json -Depth 5) -ForegroundColor Red
            } catch {
                Write-Host $errorMessage -ForegroundColor Red
            }
        } else {
            Write-Host $_.Exception.Message -ForegroundColor Red
        }
        Write-Host ""
    }
    
    Start-Sleep -Seconds 1
}

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Test forgot-password" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

foreach ($phone in @("0748526787", "0576009958")) {
    Write-Host "[TEST] forgot-password avec $phone" -ForegroundColor Yellow
    Write-Host ""
    
    $body = @{
        telephone = $phone
    } | ConvertTo-Json
    
    try {
        $response = Invoke-RestMethod -Uri "$baseUrl/auth/forgot-password" `
            -Method Post `
            -Headers @{
                "Content-Type" = "application/json"
                "Accept" = "application/json"
            } `
            -Body $body
        
        Write-Host "✓ SUCCES!" -ForegroundColor Green
        Write-Host ($response | ConvertTo-Json -Depth 5) -ForegroundColor Gray
        Write-Host ""
    } catch {
        $statusCode = $_.Exception.Response.StatusCode.value__
        $errorMessage = $_.ErrorDetails.Message
        
        Write-Host "✗ ERREUR $statusCode" -ForegroundColor Red
        
        if ($errorMessage) {
            try {
                $errorJson = $errorMessage | ConvertFrom-Json
                Write-Host ($errorJson | ConvertTo-Json -Depth 5) -ForegroundColor Red
            } catch {
                Write-Host $errorMessage -ForegroundColor Red
            }
        } else {
            Write-Host $_.Exception.Message -ForegroundColor Red
        }
        Write-Host ""
    }
    
    Start-Sleep -Seconds 1
}

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Tests termines" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
