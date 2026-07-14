@echo off
chcp 65001 >nul
echo ========================================
echo   Test API Orange SMS Directement
echo ========================================
echo.

set ORANGE_BASE=https://api.orange.com
set AUTH_HEADER=Basic WG12SzhtQVcxRm9GYWFLMElIbHNibVd0OG5jZXFzNUM6cE1OOHFudlF6OW01RzYzMnVrTHJNZnZ6VEVXOUtoNEFNOEppcUtKclJHZEk=
set SENDER_NUMBER=00000000
set PHONE1=0576009958
set PHONE2=0748526787

echo [1] Obtention du token d'acces Orange...
echo.
curl -X POST "%ORANGE_BASE%/oauth/v3/token" ^
  -H "Authorization: %AUTH_HEADER%" ^
  -H "Content-Type: application/x-www-form-urlencoded" ^
  -d "grant_type=client_credentials" ^
  -o token_response.json

echo.
echo Reponse sauvegardee dans token_response.json
echo.

REM Extraire le token (necessite jq ou parsing manuel)
echo [2] Lecture du token...
for /f "tokens=*" %%a in ('type token_response.json ^| findstr "access_token"') do (
    echo Token obtenu: %%a
)
echo.

echo [3] Test envoi SMS pour %PHONE1%...
echo Formatage du numero: 225%PHONE1:~1%
set FORMATTED_PHONE1=225%PHONE1:~1%
set SENDER_ADDR=tel:+225%SENDER_NUMBER%

echo Numero formate: %FORMATTED_PHONE1%
echo Sender: %SENDER_ADDR%
echo.

REM Note: Pour utiliser le token, il faut le recuperer depuis token_response.json
echo IMPORTANT: Copiez le token depuis token_response.json et utilisez-le dans test_orange_send_sms.bat
echo.
echo Exemple de commande avec token:
echo curl -X POST "%ORANGE_BASE%/smsmessaging/v1/outbound/%SENDER_ADDR%/requests" ^
echo   -H "Authorization: Bearer VOTRE_TOKEN_ICI" ^
echo   -H "Content-Type: application/json" ^
echo   -d "{\"outboundSMSMessageRequest\":{\"address\":\"tel:%FORMATTED_PHONE1%\",\"senderAddress\":\"%SENDER_ADDR%\",\"outboundSMSTextMessage\":{\"message\":\"Test SMS Domini\"}}}"
echo.

pause
