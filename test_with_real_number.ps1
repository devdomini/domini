# Test avec le numero reel de la base de donnees
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Test avec numero reel de la BD" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$baseUrl = "http://localhost:8000/api"

# Numero visible dans l'image : +2250576009958
# Testons differents formats
$testNumbers = @(
    "+2250576009958",
    "2250576009958",
    "0576009958",
    "225748526787",
    "0748526787"
)

foreach ($phone in $testNumbers) {
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
        
        Write-Host "SUCCES (HTTP $($response.StatusCode))!" -ForegroundColor Green
        $response.Content | ConvertFrom-Json | ConvertTo-Json -Depth 5 | Write-Host -ForegroundColor Gray
        Write-Host ""
    } catch {
        $statusCode = $_.Exception.Response.StatusCode.value__
        Write-Host "ERREUR HTTP $statusCode" -ForegroundColor Red
        
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $reader.BaseStream.Position = 0
        $reader.DiscardBufferedData()
        $responseBody = $reader.ReadToEnd()
        
        try {
            $errorJson = $responseBody | ConvertFrom-Json
            Write-Host "Message: $($errorJson.message)" -ForegroundColor Red
            if ($errorJson.errors.telephone) {
                Write-Host "Erreur: $($errorJson.errors.telephone[0])" -ForegroundColor Red
            }
        } catch {
            Write-Host "Reponse: $responseBody" -ForegroundColor Red
        }
        Write-Host ""
    }
    
    Start-Sleep -Seconds 1
}

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Tests termines" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
