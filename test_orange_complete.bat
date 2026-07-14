@echo off
chcp 65001 >nul
echo ========================================
echo   Test Complet API Orange SMS
echo ========================================
echo.

set ORANGE_BASE=https://api.orange.com
set AUTH_HEADER=Basic WG12SzhtQVcxRm9GYWFLMElIbHNibVd0OG5jZXFzNUM6cE1OOHFudlF6OW01RzYzMnVrTHJNZnZ6VEVXOUtoNEFNOEppcUtKclJHZEk=
set PHONE1=0576009958
set PHONE2=0748526787
set SENDER_NUMBER=00000000

echo [ETAPE 1] Obtention du token d'acces...
echo.
curl -X POST "%ORANGE_BASE%/oauth/v3/token" ^
  -H "Authorization: %AUTH_HEADER%" ^
  -H "Content-Type: application/x-www-form-urlencoded" ^
  -d "grant_type=client_credentials" ^
  -o token_temp.json 2>nul

echo.
echo Token sauvegarde dans token_temp.json
echo.

REM Note: Windows batch ne peut pas facilement parser JSON
REM Il faut extraire le token manuellement ou utiliser PowerShell
echo.
echo [ETAPE 2] Extraction du token...
echo.
echo IMPORTANT: Ouvrez token_temp.json et copiez la valeur de "access_token"
echo Puis appuyez sur une touche pour continuer...
pause

set /p TOKEN="Collez le token ici: "

if "%TOKEN%"=="" (
    echo ERREUR: Token non fourni
    del token_temp.json 2>nul
    pause
    exit /b 1
)

echo.
echo [ETAPE 3] Formatage des numeros...
set FORMATTED_PHONE1=225%PHONE1:~1%
set FORMATTED_PHONE2=225%PHONE2:~1%
set SENDER_ADDR=tel:+225%SENDER_NUMBER%

echo Numero 1: %PHONE1% -^> %FORMATTED_PHONE1%
echo Numero 2: %PHONE2% -^> %FORMATTED_PHONE2%
echo.

echo [ETAPE 4] Envoi SMS pour %PHONE1%...
echo.
curl -X POST "%ORANGE_BASE%/smsmessaging/v1/outbound/%SENDER_ADDR%/requests" ^
  -H "Authorization: Bearer %TOKEN%" ^
  -H "Content-Type: application/json" ^
  -d "{\"outboundSMSMessageRequest\":{\"address\":\"tel:%FORMATTED_PHONE1%\",\"senderAddress\":\"%SENDER_ADDR%\",\"outboundSMSTextMessage\":{\"message\":\"Test SMS Domini - Numero 1\"}}}"

echo.
echo.

echo [ETAPE 5] Envoi SMS pour %PHONE2%...
echo.
curl -X POST "%ORANGE_BASE%/smsmessaging/v1/outbound/%SENDER_ADDR%/requests" ^
  -H "Authorization: Bearer %TOKEN%" ^
  -H "Content-Type: application/json" ^
  -d "{\"outboundSMSMessageRequest\":{\"address\":\"tel:%FORMATTED_PHONE2%\",\"senderAddress\":\"%SENDER_ADDR%\",\"outboundSMSTextMessage\":{\"message\":\"Test SMS Domini - Numero 2\"}}}"

echo.
echo.

echo [ETAPE 6] Nettoyage...
del token_temp.json 2>nul

echo ========================================
echo   Tests termines
echo ========================================
echo.
echo Verifiez les SMS sur:
echo - %PHONE1%
echo - %PHONE2%
echo.
pause
