# Configuration du pare-feu Windows pour Laravel
# IMPORTANT : Ce script doit être exécuté en tant qu'Administrateur

Write-Host "=======================================" -ForegroundColor Cyan
Write-Host "Configuration du pare-feu Windows" -ForegroundColor Cyan
Write-Host "=======================================" -ForegroundColor Cyan
Write-Host ""

# Vérifier si on est administrateur
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    Write-Host "ERREUR : Ce script doit être exécuté en tant qu'Administrateur !" -ForegroundColor Red
    Write-Host ""
    Write-Host "Pour exécuter en tant qu'Administrateur :" -ForegroundColor Yellow
    Write-Host "1. Ouvrir PowerShell en tant qu'Administrateur" -ForegroundColor Yellow
    Write-Host "2. Exécuter : cd 'C:\Users\JEAN SERI\Desktop\domini\domini'" -ForegroundColor Yellow
    Write-Host "3. Exécuter : .\configurer_parefeu.ps1" -ForegroundColor Yellow
    Write-Host ""
    exit 1
}

Write-Host "Vérification des règles existantes..." -ForegroundColor Yellow

# Vérifier si la règle existe déjà
$existingRule = Get-NetFirewallRule -DisplayName "Laravel Dev Server" -ErrorAction SilentlyContinue

if ($existingRule) {
    Write-Host "La règle 'Laravel Dev Server' existe déjà." -ForegroundColor Green
    Write-Host "Suppression de l'ancienne règle..." -ForegroundColor Yellow
    Remove-NetFirewallRule -DisplayName "Laravel Dev Server"
}

Write-Host "Création de la nouvelle règle..." -ForegroundColor Green

try {
    New-NetFirewallRule -DisplayName "Laravel Dev Server" `
                        -Direction Inbound `
                        -LocalPort 8000 `
                        -Protocol TCP `
                        -Action Allow `
                        -Profile Any `
                        -Enabled True
    
    Write-Host ""
    Write-Host "=======================================" -ForegroundColor Green
    Write-Host "SUCCÈS !" -ForegroundColor Green
    Write-Host "=======================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "Le pare-feu Windows autorise maintenant les connexions" -ForegroundColor Green
    Write-Host "sur le port 8000 pour le serveur Laravel." -ForegroundColor Green
    Write-Host ""
    Write-Host "Vous pouvez maintenant tester l'application mobile !" -ForegroundColor Cyan
    Write-Host ""
    
} catch {
    Write-Host ""
    Write-Host "=======================================" -ForegroundColor Red
    Write-Host "ERREUR" -ForegroundColor Red
    Write-Host "=======================================" -ForegroundColor Red
    Write-Host ""
    Write-Host $_.Exception.Message -ForegroundColor Red
    exit 1
}
