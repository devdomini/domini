@echo off
echo ========================================
echo   Reparation de la Base de Donnees
echo ========================================
echo.

echo Suppression et recreation de la base de donnees domini...
echo.

REM Trouver le chemin MySQL de XAMPP
set MYSQL_PATH=C:\xampp\mysql\bin\mysql.exe

if not exist "%MYSQL_PATH%" (
    echo ERREUR: MySQL introuvable dans C:\xampp\mysql\bin
    echo Veuillez ajuster le chemin dans ce script.
    pause
    exit /b 1
)

echo Utilisation de: %MYSQL_PATH%
echo.

"%MYSQL_PATH%" -u root -e "DROP DATABASE IF EXISTS domini; CREATE DATABASE domini CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

if %errorlevel% equ 0 (
    echo OK - Base de donnees recreee
    echo.
    echo Lancement des migrations...
    php artisan migrate --force
    
    if %errorlevel% equ 0 (
        echo.
        echo ========================================
        echo   Base de donnees reparee avec succes!
        echo ========================================
    ) else (
        echo.
        echo ERREUR lors des migrations
    )
) else (
    echo ERREUR lors de la recreation de la base de donnees
)

echo.
pause
