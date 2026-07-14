@echo off
chcp 65001 >nul
echo ========================================
echo   Obtenir Token Orange SMS
echo ========================================
echo.

set ORANGE_BASE=https://api.orange.com
set AUTH_HEADER=Basic WG12SzhtQVcxRm9GYWFLMElIbHNibVd0OG5jZXFzNUM6cE1OOHFudlF6OW01RzYzMnVrTHJNZnZ6VEVXOUtoNEFNOEppcUtKclJHZEk=

echo Envoi de la requete pour obtenir le token...
echo.

curl -X POST "%ORANGE_BASE%/oauth/v3/token" ^
  -H "Authorization: %AUTH_HEADER%" ^
  -H "Content-Type: application/x-www-form-urlencoded" ^
  -d "grant_type=client_credentials"

echo.
echo.
echo ========================================
echo   Instructions
echo ========================================
echo.
echo 1. Copiez le "access_token" de la reponse ci-dessus
echo 2. Utilisez-le dans test_orange_send_sms.bat
echo 3. Ou utilisez directement avec curl (voir exemple ci-dessous)
echo.
pause
