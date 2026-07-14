@echo off
echo ========================================
echo   Redemarrage du Serveur Laravel
echo ========================================
echo.

echo Arret des processus PHP sur le port 8000...
for /f "tokens=5" %%a in ('netstat -ano ^| findstr :8000 ^| findstr LISTENING') do (
    echo Tentative d'arret du processus %%a
    taskkill /F /PID %%a 2>nul
)

echo.
echo Nettoyage du cache...
php artisan optimize:clear
php artisan route:clear
php artisan config:clear

echo.
echo Demarrage du serveur sur 0.0.0.0:8000...
start "Laravel Server" cmd /k "php artisan serve --host=0.0.0.0 --port=8000"

echo.
echo Serveur demarre!
echo Testez avec: http://192.168.1.134:8000/api/entreprises
echo.
pause
