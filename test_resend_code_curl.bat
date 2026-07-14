@echo off
chcp 65001 >nul
echo ========================================
echo   Test Route resendVerificationCode (curl)
echo ========================================
echo.

set BASE_URL=http://localhost:8000/api
set PHONE_WITH_CODE=225748526787
set PHONE_LOCAL=0748526787

echo IMPORTANT: Le numero doit exister dans la base de donnees!
echo.
echo Format attendu dans la BD: %PHONE_LOCAL% (format local)
echo Format fourni: %PHONE_WITH_CODE% (format avec indicatif)
echo.

echo [TEST 1] Avec format indicatif (%PHONE_WITH_CODE%)...
echo.
curl.exe -X POST "%BASE_URL%/auth/resend-verification-code" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"telephone\": \"%PHONE_WITH_CODE%\"}"

echo.
echo.

echo [TEST 2] Avec format local (%PHONE_LOCAL%)...
echo.
curl.exe -X POST "%BASE_URL%/auth/resend-verification-code" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"telephone\": \"%PHONE_LOCAL%\"}"

echo.
echo.
echo ========================================
echo   Tests termines
echo ========================================
echo.
echo Verifiez le SMS sur le numero %PHONE_LOCAL%
echo.
pause
