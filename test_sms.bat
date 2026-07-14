@echo off
chcp 65001 >nul
echo ========================================
echo   Test SMS - Envoi de codes de verification
echo ========================================
echo.

set BASE_URL=http://localhost:8000/api
set PHONE1=0576009958
set PHONE2=0748526787

echo IMPORTANT: Ces numeros doivent exister dans la base de donnees!
echo.
echo Test 1: Renvoyer code de verification pour %PHONE1%
echo.
curl -X POST "%BASE_URL%/auth/resend-verification-code" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"telephone\": \"%PHONE1%\"}"
echo.
echo.

echo Test 2: Renvoyer code de verification pour %PHONE2%
echo.
curl -X POST "%BASE_URL%/auth/resend-verification-code" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"telephone\": \"%PHONE2%\"}"
echo.
echo.

echo Test 3: Mot de passe oublie pour %PHONE1%
echo.
curl -X POST "%BASE_URL%/auth/forgot-password" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"telephone\": \"%PHONE1%\"}"
echo.
echo.

echo Test 4: Mot de passe oublie pour %PHONE2%
echo.
curl -X POST "%BASE_URL%/auth/forgot-password" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"telephone\": \"%PHONE2%\"}"
echo.
echo.

echo ========================================
echo   Tests termines
echo ========================================
echo.
echo Verifiez vos SMS sur les numeros:
echo - %PHONE1%
echo - %PHONE2%
echo.
echo Note: Si vous obtenez "Ce numero de telephone n'existe pas",
echo       vous devez d'abord creer un compte avec ces numeros.
echo.
pause
