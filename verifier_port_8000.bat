@echo off
echo ========================================
echo Verification du port 8000
echo ========================================
echo.

echo 1. Verification des regles de pare-feu pour le port 8000:
echo.
netsh advfirewall firewall show rule name="Laravel API Port 8000"
echo.

echo 2. Verification si le port 8000 est en ecoute:
echo.
netstat -an | findstr ":8000"
echo.

echo 3. Test de connexion local:
echo.
curl -s http://localhost:8000/api/entreprises 2>nul
if %errorLevel% equ 0 (
    echo SUCCES: Le serveur repond sur localhost:8000
) else (
    echo ERREUR: Le serveur ne repond pas sur localhost:8000
    echo Assurez-vous que le serveur Laravel est demarre avec:
    echo php artisan serve --host=0.0.0.0 --port=8000
)
echo.

pause
