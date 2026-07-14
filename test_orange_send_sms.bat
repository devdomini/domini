@echo off
chcp 65001 >nul
echo ========================================
echo   Envoyer SMS via API Orange
echo ========================================
echo.

if "%1"=="" (
    echo Usage: test_orange_send_sms.bat [TOKEN] [NUMERO] [MESSAGE]
    echo.
    echo Exemple:
    echo   test_orange_send_sms.bat YOUR_TOKEN_HERE 0576009958 "Test SMS"
    echo.
    echo Pour obtenir un token, executez: test_orange_get_token.bat
    echo.
    pause
    exit /b 1
)

set TOKEN=%1
set PHONE=%2
set MESSAGE=%3

if "%TOKEN%"=="" (
    echo ERREUR: Token manquant
    pause
    exit /b 1
)

if "%PHONE%"=="" (
    set PHONE=0576009958
    echo Utilisation du numero par defaut: %PHONE%
)

if "%MESSAGE%"=="" (
    set MESSAGE=Test SMS Domini
    echo Utilisation du message par defaut: %MESSAGE%
)

set ORANGE_BASE=https://api.orange.com
set SENDER_NUMBER=00000000

REM Formater le numero (0XXXXXXXXX -> 225XXXXXXXXX)
if "%PHONE:~0,1%"=="0" (
    set FORMATTED_PHONE=225%PHONE:~1%
) else (
    set FORMATTED_PHONE=%PHONE%
)

set SENDER_ADDR=tel:+225%SENDER_NUMBER%
set ENDPOINT=%ORANGE_BASE%/smsmessaging/v1/outbound/%SENDER_ADDR%/requests

echo Configuration:
echo   Token: %TOKEN:~0,20%...
echo   Numero: %PHONE% (formate: %FORMATTED_PHONE%)
echo   Message: %MESSAGE%
echo   Sender: %SENDER_ADDR%
echo   Endpoint: %ENDPOINT%
echo.

echo Envoi du SMS...
echo.

curl -X POST "%ENDPOINT%" ^
  -H "Authorization: Bearer %TOKEN%" ^
  -H "Content-Type: application/json" ^
  -d "{\"outboundSMSMessageRequest\":{\"address\":\"tel:%FORMATTED_PHONE%\",\"senderAddress\":\"%SENDER_ADDR%\",\"outboundSMSTextMessage\":{\"message\":\"%MESSAGE%\"}}}"

echo.
echo.
echo ========================================
echo   Test termine
echo ========================================
echo.
echo Verifiez le SMS sur le numero %PHONE%
echo.
pause
