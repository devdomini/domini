# Script PowerShell pour corriger le problème de tablespace MySQL
# Usage: .\fix_tablespace.ps1

Write-Host "🔧 Correction du problème de tablespace MySQL..." -ForegroundColor Cyan
Write-Host ""

# Demander les informations de connexion MySQL
$mysqlUser = Read-Host "Nom d'utilisateur MySQL (par défaut: root)"
if ([string]::IsNullOrWhiteSpace($mysqlUser)) {
    $mysqlUser = "root"
}

$mysqlPassword = Read-Host "Mot de passe MySQL" -AsSecureString
$mysqlPasswordPlain = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($mysqlPassword))

$database = Read-Host "Nom de la base de données (par défaut: laravel)"
if ([string]::IsNullOrWhiteSpace($database)) {
    $database = "laravel"
}

Write-Host ""
Write-Host "Tentative de connexion à MySQL..." -ForegroundColor Yellow

# Créer un fichier SQL temporaire
$sqlFile = "temp_fix_tablespace.sql"
@"
USE $database;

-- Supprimer le tablespace
SET @table_exists = (SELECT COUNT(*) FROM information_schema.tables 
                      WHERE table_schema = '$database' AND table_name = 'migrations');

SET @sql = IF(@table_exists > 0, 
    'ALTER TABLE migrations DISCARD TABLESPACE', 
    'SELECT "Table migrations does not exist" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Supprimer la table
DROP TABLE IF EXISTS migrations;
DROP TABLE IF EXISTS cache;
DROP TABLE IF EXISTS cache_locks;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS jobs;
DROP TABLE IF EXISTS failed_jobs;

SELECT 'Tables supprimées avec succès!' AS result;
"@ | Out-File -FilePath $sqlFile -Encoding UTF8

try {
    # Exécuter le script SQL
    $result = & mysql -u $mysqlUser -p$mysqlPasswordPlain $database -e "source $sqlFile" 2>&1
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Tablespace supprimé avec succès!" -ForegroundColor Green
        Write-Host ""
        Write-Host "Vous pouvez maintenant exécuter: php artisan migrate" -ForegroundColor Green
    } else {
        Write-Host "❌ Erreur lors de l'exécution:" -ForegroundColor Red
        Write-Host $result
        Write-Host ""
        Write-Host "💡 Solution alternative:" -ForegroundColor Yellow
        Write-Host "1. Connectez-vous à MySQL: mysql -u $mysqlUser -p" -ForegroundColor White
        Write-Host "2. Utilisez la base: USE $database;" -ForegroundColor White
        Write-Host "3. Exécutez: ALTER TABLE migrations DISCARD TABLESPACE;" -ForegroundColor White
        Write-Host "4. Exécutez: DROP TABLE IF EXISTS migrations;" -ForegroundColor White
        Write-Host "5. Relancez: php artisan migrate" -ForegroundColor White
    }
} catch {
    Write-Host "❌ Erreur: $_" -ForegroundColor Red
    Write-Host ""
    Write-Host "💡 Assurez-vous que MySQL est installé et accessible dans le PATH" -ForegroundColor Yellow
    Write-Host "Ou exécutez manuellement le fichier: fix_tablespace_mysql.sql" -ForegroundColor Yellow
} finally {
    # Nettoyer le fichier temporaire
    if (Test-Path $sqlFile) {
        Remove-Item $sqlFile -Force
    }
}
