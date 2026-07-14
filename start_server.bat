@echo off
echo ========================================
echo   Demarrage du serveur Laravel Domini
echo   Accessible sur le reseau local
echo ========================================
echo.
echo Le serveur sera accessible sur:
echo   - http://0.0.0.0:8000
echo   - http://localhost:8000
echo   - http://[VOTRE_IP_LOCALE]:8000
echo.
echo Pour trouver votre IP locale:
echo   ipconfig | findstr IPv4
echo.
echo Appuyez sur Ctrl+C pour arreter le serveur
echo.
php artisan serve --host=0.0.0.0 --port=8000
