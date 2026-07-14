# Diagnostic complet du service Orange SMS
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Diagnostic Orange SMS" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$orangeBase = "https://api.orange.com"
$authHeader = "Basic WG12SzhtQVcxRm9GYWFLMElIbHNibVd0OG5jZXFzNUM6cE1OOHFudlF6OW01RzYzMnVrTHJNZnZ6VEVXOUtoNEFNOEppcUtKclJHZEk="
$senderName = "SMS 487507"
$phone = "225576009958"

Write-Host "[ETAPE 1] Obtention du token d'acces..." -ForegroundColor Yellow
Write-Host ""

try {
    $tokenResponse = Invoke-RestMethod -Uri "$orangeBase/oauth/v3/token" `
        -Method Post `
        -Headers @{
            "Authorization" = $authHeader
            "Content-Type" = "application/x-www-form-urlencoded"
        } `
        -Body "grant_type=client_credentials"
    
    $accessToken = $tokenResponse.access_token
    
    if ($accessToken) {
        Write-Host "SUCCES: Token obtenu" -ForegroundColor Green
        Write-Host "Token: $($accessToken.Substring(0, 30))..." -ForegroundColor Gray
        Write-Host "Expires in: $($tokenResponse.expires_in) secondes" -ForegroundColor Gray
        Write-Host ""
    } else {
        Write-Host "ERREUR: Token non trouve dans la reponse" -ForegroundColor Red
        Write-Host ($tokenResponse | ConvertTo-Json) -ForegroundColor Red
        exit 1
    }
} catch {
    Write-Host "ERREUR lors de l'obtention du token" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    if ($_.ErrorDetails.Message) {
        Write-Host $_.ErrorDetails.Message -ForegroundColor Red
    }
    exit 1
}

Write-Host "[ETAPE 2] Preparation de l'envoi SMS..." -ForegroundColor Yellow
Write-Host ""

# IMPORTANT: Orange exige que le senderAddress commence par "tel:" même pour un sender name
# Le senderAddress dans le body doit correspondre EXACTEMENT à celui dans l'URL après encodage
# L'URL encode les espaces en '+', donc on doit utiliser '+' dans le body aussi
$senderAddrForBody = "tel:$($senderName -replace ' ', '+')"
$senderAddrEncoded = [System.Web.HttpUtility]::UrlEncode($senderAddrForBody)
$endpoint = "$orangeBase/smsmessaging/v1/outbound/$senderAddrEncoded/requests"

Write-Host "Endpoint: $endpoint" -ForegroundColor Gray
Write-Host "Numero destinataire: $phone" -ForegroundColor Gray
Write-Host "Sender Address (Body): $senderAddrForBody" -ForegroundColor Gray
Write-Host "Sender Address (URL): $senderAddrEncoded" -ForegroundColor Gray
Write-Host ""

$payload = @{
    outboundSMSMessageRequest = @{
        address = "tel:$phone"
        senderAddress = $senderAddrForBody
        outboundSMSTextMessage = @{
            message = "Test SMS Domini - Diagnostic"
        }
    }
} | ConvertTo-Json -Depth 10

Write-Host "Payload:" -ForegroundColor Gray
Write-Host $payload -ForegroundColor Gray
Write-Host ""

Write-Host "[ETAPE 3] Envoi du SMS..." -ForegroundColor Yellow
Write-Host ""

try {
    $response = Invoke-RestMethod -Uri $endpoint `
        -Method Post `
        -Headers @{
            "Authorization" = "Bearer $accessToken"
            "Content-Type" = "application/json"
        } `
        -Body $payload
    
    Write-Host "SUCCES: SMS envoye!" -ForegroundColor Green
    Write-Host ($response | ConvertTo-Json -Depth 5) -ForegroundColor Gray
} catch {
    Write-Host "ERREUR lors de l'envoi du SMS" -ForegroundColor Red
    Write-Host "Status Code: $($_.Exception.Response.StatusCode.value__)" -ForegroundColor Red
    
    $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
    $reader.BaseStream.Position = 0
    $reader.DiscardBufferedData()
    $errorBody = $reader.ReadToEnd()
    
    Write-Host ""
    Write-Host "Reponse d'erreur:" -ForegroundColor Yellow
    Write-Host $errorBody -ForegroundColor Red
    
    try {
        $errorJson = $errorBody | ConvertFrom-Json
        Write-Host ""
        Write-Host "JSON parse:" -ForegroundColor Yellow
        Write-Host ($errorJson | ConvertTo-Json -Depth 5) -ForegroundColor Red
        
        if ($errorJson.requestError) {
            Write-Host ""
            Write-Host "Details de l'erreur Orange:" -ForegroundColor Yellow
            if ($errorJson.requestError.serviceException) {
                Write-Host "Message ID: $($errorJson.requestError.serviceException.messageId)" -ForegroundColor Red
                Write-Host "Text: $($errorJson.requestError.serviceException.text)" -ForegroundColor Red
            }
        }
    } catch {
        Write-Host "(Impossible de parser le JSON)" -ForegroundColor Gray
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Diagnostic termine" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
