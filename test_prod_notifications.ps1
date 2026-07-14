$telephone = "+2250576009958"
$password = "00000000"
$baseUrl = "https://domini-food.com/api"

Write-Host "Tentative de connexion à $baseUrl/auth/login..."

try {
    $loginBody = @{
        telephone = $telephone
        password = $password
    }
    
    $loginResponse = Invoke-WebRequest -Uri "$baseUrl/auth/login" -Method Post -Body $loginBody -ContentType "application/x-www-form-urlencoded" -UseBasicParsing
    $loginJson = $loginResponse.Content | ConvertFrom-Json
    
    if ($loginJson.success -eq $true) {
        $token = $loginJson.data.access_token
        Write-Host "Connexion réussie! Token récupéré."
        
        Write-Host "Test de récupération des notifications..."
        try {
            $notifResponse = Invoke-WebRequest -Uri "$baseUrl/notifications" -Method Get -Headers @{ "Authorization" = "Bearer $token"; "Accept" = "application/json" } -UseBasicParsing
            
            Write-Host "Réponse notifications (Status Code: $($notifResponse.StatusCode)):"
            Write-Host $notifResponse.Content
        }
        catch {
            Write-Host "Erreur lors de la récupération des notifications:"
            Write-Host $_.Exception.Message
            if ($_.Exception.Response) {
                $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
                Write-Host $reader.ReadToEnd()
            }
        }
    }
    else {
        Write-Host "Échec de la connexion: $($loginJson.message)"
    }
}
catch {
    Write-Host "Erreur lors de la connexion:"
    Write-Host $_.Exception.Message
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        Write-Host "Détails de l'erreur:"
        Write-Host $reader.ReadToEnd()
    }
}
