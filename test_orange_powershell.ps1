# Test API Orange SMS avec PowerShell
# PowerShell peut parser JSON facilement

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Test API Orange SMS (PowerShell)" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$orangeBase = "https://api.orange.com"
$authHeader = "Basic WG12SzhtQVcxRm9GYWFLMElIbHNibVd0OG5jZXFzNUM6cE1OOHFudlF6OW01RzYzMnVrTHJNZnZ6VEVXOUtoNEFNOEppcUtKclJHZEk="
$senderNumber = "00000000"
$phone1 = "0576009958"
$phone2 = "0748526787"

# Formater les numéros (0XXXXXXXXX -> 225XXXXXXXXX)
$formattedPhone1 = "225" + $phone1.Substring(1)
$formattedPhone2 = "225" + $phone2.Substring(1)
$senderAddr = "tel:+225$senderNumber"

Write-Host "[ETAPE 1] Obtention du token d'accès..." -ForegroundColor Yellow
Write-Host ""

$tokenResponse = Invoke-RestMethod -Uri "$orangeBase/oauth/v3/token" `
    -Method Post `
    -Headers @{
        "Authorization" = $authHeader
        "Content-Type" = "application/x-www-form-urlencoded"
    } `
    -Body "grant_type=client_credentials"

$accessToken = $tokenResponse.access_token

if ($accessToken) {
    Write-Host "✓ Token obtenu: $($accessToken.Substring(0, 20))..." -ForegroundColor Green
    Write-Host ""
    
    Write-Host "[ETAPE 2] Envoi SMS pour $phone1..." -ForegroundColor Yellow
    Write-Host ""
    
    $payload1 = @{
        outboundSMSMessageRequest = @{
            address = "tel:$formattedPhone1"
            senderAddress = $senderAddr
            outboundSMSTextMessage = @{
                message = "Test SMS Domini - Numero 1"
            }
        }
    } | ConvertTo-Json -Depth 10
    
    try {
        $response1 = Invoke-RestMethod -Uri "$orangeBase/smsmessaging/v1/outbound/$([System.Web.HttpUtility]::UrlEncode($senderAddr))/requests" `
            -Method Post `
            -Headers @{
                "Authorization" = "Bearer $accessToken"
                "Content-Type" = "application/json"
            } `
            -Body $payload1
        
        Write-Host "✓ SMS envoyé avec succès pour $phone1" -ForegroundColor Green
        Write-Host "Réponse: $($response1 | ConvertTo-Json)" -ForegroundColor Gray
    } catch {
        Write-Host "✗ Erreur lors de l'envoi pour $phone1" -ForegroundColor Red
        Write-Host "Erreur: $($_.Exception.Message)" -ForegroundColor Red
    }
    
    Write-Host ""
    Write-Host "[ETAPE 3] Envoi SMS pour $phone2..." -ForegroundColor Yellow
    Write-Host ""
    
    $payload2 = @{
        outboundSMSMessageRequest = @{
            address = "tel:$formattedPhone2"
            senderAddress = $senderAddr
            outboundSMSTextMessage = @{
                message = "Test SMS Domini - Numero 2"
            }
        }
    } | ConvertTo-Json -Depth 10
    
    try {
        $response2 = Invoke-RestMethod -Uri "$orangeBase/smsmessaging/v1/outbound/$([System.Web.HttpUtility]::UrlEncode($senderAddr))/requests" `
            -Method Post `
            -Headers @{
                "Authorization" = "Bearer $accessToken"
                "Content-Type" = "application/json"
            } `
            -Body $payload2
        
        Write-Host "✓ SMS envoyé avec succès pour $phone2" -ForegroundColor Green
        Write-Host "Réponse: $($response2 | ConvertTo-Json)" -ForegroundColor Gray
    } catch {
        Write-Host "✗ Erreur lors de l'envoi pour $phone2" -ForegroundColor Red
        Write-Host "Erreur: $($_.Exception.Message)" -ForegroundColor Red
    }
    
} else {
    Write-Host "✗ Impossible d'obtenir le token" -ForegroundColor Red
    Write-Host "Réponse: $($tokenResponse | ConvertTo-Json)" -ForegroundColor Gray
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Tests terminés" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Vérifiez les SMS sur:" -ForegroundColor Yellow
Write-Host "- $phone1" -ForegroundColor White
Write-Host "- $phone2" -ForegroundColor White
Write-Host ""
