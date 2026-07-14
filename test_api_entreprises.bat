@echo off
echo Test de l'API Entreprises
echo.

echo Test avec localhost:
curl http://localhost:8000/api/entreprises
echo.
echo.

echo Test avec IP externe:
curl http://192.168.1.3:8000/api/entreprises
echo.
echo.

pause
