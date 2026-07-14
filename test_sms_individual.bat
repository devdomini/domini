@echo off
chcp 65001 >nul
echo ========================================
echo   Test SMS Individual
echo ========================================
echo.

set BASE_URL=http://localhost:8000/api

if "%1"=="" (
    echo Usage: test_sms_individual.bat [numero]
    echo Exemple: test_sms_individual.bat 0576009958
    echo.
    pause
    exit /b 1
)

set PHONE=%1

echo Test: Renvoyer code de verification pour %PHONE%
echo.
curl -X POST "%BASE_URL%/auth/resend-verification-code" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"telephone\": \"%PHONE%\"}"
echo.
echo.

echo Test: Mot de passe oublie pour %PHONE%
echo.
curl -X POST "%BASE_URL%/auth/forgot-password" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"telephone\": \"%PHONE%\"}"
echo.
echo.

pause
