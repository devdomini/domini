@echo off
echo ========================================
echo   Reparation Complete de la Base de Donnees
echo ========================================
echo.

cd /d "%~dp0"

echo Etape 1: Suppression des fichiers .ibd et .frm restants...
set MYSQL_DATA=C:\xampp\mysql\data\domini

if exist "%MYSQL_DATA%\*.ibd" (
    echo Suppression des fichiers .ibd...
    del /F /Q "%MYSQL_DATA%\*.ibd"
)

if exist "%MYSQL_DATA%\*.frm" (
    echo Suppression des fichiers .frm...
    del /F /Q "%MYSQL_DATA%\*.frm"
)

echo.
echo Etape 2: Suppression des tables via MySQL...
C:\xampp\mysql\bin\mysql.exe -u root -e "USE domini; DROP TABLE IF EXISTS migrations, cache, cache_locks, sessions, users, password_reset_tokens, personal_access_tokens, categories, plats, commandes, commande_items, favoris, paiements, livraisons;" 2>nul

echo.
echo Etape 3: Lancement des migrations...
php artisan migrate --force

if %errorlevel% equ 0 (
    echo.
    echo ========================================
    echo   Base de donnees reparee avec succes!
    echo ========================================
    echo.
    echo Test de l'API...
    curl http://localhost:8000/api/menu/categories
) else (
    echo.
    echo ERREUR lors des migrations
    echo Verifiez les logs dans storage\logs\laravel.log
)

echo.
pause
