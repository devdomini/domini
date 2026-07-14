@echo off
chcp 65001 >nul
echo ========================================
echo   Test Route resendVerificationCode
echo ========================================
echo.

set BASE_URL=http://localhost:8000/api
set PHONE=225748526787

echo IMPORTANT: Le numero doit exister dans la base de donnees!
echo.
echo Format attendu dans la BD: 0748526787 (format local)
echo Format fourni: %PHONE% (format avec indicatif)
echo.
echo Si le numero n'existe pas, essayez avec le format local: 0748526787
echo.

echo [TEST 1] Avec format indicatif (%PHONE%)...
echo.
curl.exe -X POST "%BASE_URL%/auth/resend-verification-code" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"telephone\": \"%PHONE%\"}"

echo.
echo.

echo [TEST 2] Avec format local (0748526787)...
echo.
curl.exe -X POST "%BASE_URL%/auth/resend-verification-code" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"telephone\": \"0748526787\"}"

echo.
echo.
echo ========================================
echo   Tests termines
echo ========================================
echo.
echo Verifiez le SMS sur le numero 0748526787
echo.
pause
