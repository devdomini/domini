# Test de connectivité pour l'application mobile
# Date: 2026-02-12

Write-Host "=======================================" -ForegroundColor Cyan
Write-Host "TEST DE CONNECTIVITE MOBILE" -ForegroundColor Cyan
Write-Host "=======================================" -ForegroundColor Cyan
Write-Host ""

# Obtenir l'IP locale
$ip = (Get-NetIPAddress -AddressFamily IPv4 | Where-Object {$_.InterfaceAlias -notlike "*Loopback*" -and $_.IPAddress -like "192.168.*"}).IPAddress

if ($ip) {
    Write-Host "Votre IP locale : $ip" -ForegroundColor Green
    Write-Host ""
} else {
    Write-Host "Impossible de trouver l'IP locale !" -ForegroundColor Red
    Write-Host ""
}

# Test 1 : Localhost
Write-Host "Test 1 : Connexion localhost..." -ForegroundColor Yellow
try {
    $response1 = Invoke-RestMethod -Uri "http://localhost:8000/api/entreprises" -Method Get -ErrorAction Stop -TimeoutSec 5
    Write-Host "  SUCCES - Le serveur répond sur localhost" -ForegroundColor Green
} catch {
    Write-Host "  ECHEC - Le serveur ne répond pas sur localhost" -ForegroundColor Red
    Write-Host "  Erreur: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

# Test 2 : 127.0.0.1
Write-Host "Test 2 : Connexion 127.0.0.1..." -ForegroundColor Yellow
try {
    $response2 = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/entreprises" -Method Get -ErrorAction Stop -TimeoutSec 5
    Write-Host "  SUCCES - Le serveur répond sur 127.0.0.1" -ForegroundColor Green
} catch {
    Write-Host "  ECHEC - Le serveur ne répond pas sur 127.0.0.1" -ForegroundColor Red
    Write-Host "  Erreur: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

# Test 3 : IP locale
if ($ip) {
    Write-Host "Test 3 : Connexion IP locale ($ip)..." -ForegroundColor Yellow
    try {
        $response3 = Invoke-RestMethod -Uri "http://$ip:8000/api/entreprises" -Method Get -ErrorAction Stop -TimeoutSec 5
        Write-Host "  SUCCES - Le serveur répond sur l'IP locale !" -ForegroundColor Green
        Write-Host "  L'application mobile devrait pouvoir se connecter !" -ForegroundColor Green
    } catch {
        Write-Host "  ECHEC - Le serveur ne répond pas sur l'IP locale" -ForegroundColor Red
        Write-Host "  Erreur: $($_.Exception.Message)" -ForegroundColor Red
        Write-Host ""
        Write-Host "  CAUSES POSSIBLES :" -ForegroundColor Yellow
        Write-Host "  1. Le pare-feu Windows bloque le port 8000" -ForegroundColor Yellow
        Write-Host "  2. Le serveur n'écoute pas sur 0.0.0.0" -ForegroundColor Yellow
        Write-Host ""
        Write-Host "  SOLUTION :" -ForegroundColor Cyan
        Write-Host "  Exécutez 'configurer_parefeu.ps1' en tant qu'Administrateur" -ForegroundColor Cyan
    }
} else {
    Write-Host "Test 3 : IP locale non trouvée, test ignoré" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "=======================================" -ForegroundColor Cyan
Write-Host "CONFIGURATION APP MOBILE" -ForegroundColor Cyan
Write-Host "=======================================" -ForegroundColor Cyan
Write-Host ""
if ($ip) {
    Write-Host "Dans 'dominimobile/lib/config/api_config.dart', utilisez :" -ForegroundColor Green
    Write-Host ""
    Write-Host "  static const String baseUrl = 'http://$ip:8000/api';" -ForegroundColor Yellow
    Write-Host ""
} else {
    Write-Host "Impossible de déterminer l'IP locale" -ForegroundColor Red
}

Write-Host "=======================================" -ForegroundColor Cyan
Write-Host "INSTRUCTIONS POUR TESTER" -ForegroundColor Cyan
Write-Host "=======================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. Assurez-vous que le téléphone est sur le même WiFi" -ForegroundColor Yellow
Write-Host "2. Sur le téléphone, ouvrez le navigateur" -ForegroundColor Yellow
Write-Host "3. Allez sur : http://$ip:8000/api/entreprises" -ForegroundColor Yellow
Write-Host "4. Vous devriez voir une réponse JSON" -ForegroundColor Yellow
Write-Host ""
Write-Host "Si ça ne fonctionne pas :" -ForegroundColor Cyan
Write-Host "- Exécutez 'configurer_parefeu.ps1' en tant qu'Administrateur" -ForegroundColor Yellow
Write-Host "- Vérifiez que le serveur Laravel tourne (voir terminal)" -ForegroundColor Yellow
Write-Host ""
