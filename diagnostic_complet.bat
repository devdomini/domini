@echo off
echo ========================================
echo   DIAGNOSTIC COMPLET - Connexion API
echo ========================================
echo.

echo [1] Verification de l'IP locale...
echo.
ipconfig | findstr IPv4
echo.

echo [2] Verification du port 8000...
echo.
netstat -an | findstr ":8000"
echo.

echo [3] Verification des regles de pare-feu pour le port 8000...
echo.
netsh advfirewall firewall show rule name="Laravel API Port 8000" 2>nul
if %errorLevel% neq 0 (
    echo ERREUR: La regle de pare-feu n'existe pas!
    echo Executez: autoriser_port_8000.bat en tant qu'administrateur
) else (
    echo OK: La regle de pare-feu existe
)
echo.

echo [4] Test de connexion local (localhost)...
echo.
curl -s -o nul -w "HTTP Code: %%{http_code}\n" http://localhost:8000/api/entreprises 2>nul
if %errorLevel% equ 0 (
    echo OK: Le serveur repond sur localhost
) else (
    echo ERREUR: Le serveur ne repond pas sur localhost
)
echo.

echo [5] Test de connexion avec l'IP locale (192.168.1.134)...
echo.
curl -s -o nul -w "HTTP Code: %%{http_code}\n" http://192.168.1.134:8000/api/entreprises 2>nul
if %errorLevel% equ 0 (
    echo OK: Le serveur repond sur l'IP locale
) else (
    echo ERREUR: Le serveur ne repond pas sur l'IP locale
    echo Verifiez que le pare-feu autorise le port 8000
)
echo.

echo [6] Liste des processus PHP en cours...
echo.
tasklist | findstr php.exe
echo.

echo ========================================
echo   RECOMMANDATIONS
echo ========================================
echo.
echo 1. Si le pare-feu n'est pas configure:
echo    - Executez: autoriser_port_8000.bat (en admin)
echo.
echo 2. Si le serveur ne repond pas:
echo    - Executez: restart_server.bat
echo.
echo 3. Verifiez que votre telephone est sur le meme reseau Wi-Fi
echo.
echo 4. Dans l'app Flutter, verifiez que api_config.dart contient:
echo    baseUrl = 'http://192.168.1.134:8000/api'
echo.

pause
