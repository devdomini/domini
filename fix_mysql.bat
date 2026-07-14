@echo off
echo ========================================
echo   Diagnostic et Fix MySQL
echo ========================================
echo.

echo [1/5] Verification du port 3306...
netstat -ano | findstr :3306
if %errorlevel% equ 0 (
    echo.
    echo ATTENTION: Le port 3306 est deja utilise!
    echo Veuillez arreter le processus qui utilise ce port.
    echo.
    pause
) else (
    echo OK - Le port 3306 est libre
)
echo.

echo [2/5] Verification des processus MySQL...
tasklist | findstr mysqld
if %errorlevel% equ 0 (
    echo.
    echo Des processus MySQL sont en cours d'execution.
    echo Voulez-vous les arreter? (O/N)
    set /p choice=
    if /i "%choice%"=="O" (
        echo Arret des processus MySQL...
        taskkill /F /IM mysqld.exe
        timeout /t 3 /nobreak >nul
        echo OK - Processus arretes
    )
) else (
    echo OK - Aucun processus MySQL en cours
)
echo.

echo [3/5] Verification de l'espace disque...
for /f "tokens=3" %%a in ('dir C:\ ^| findstr "bytes free"') do set freespace=%%a
echo Espace libre sur C:\: %freespace%
echo.

echo [4/5] Verification du fichier my.ini...
if exist "C:\xampp\mysql\bin\my.ini" (
    echo OK - Fichier my.ini trouve
    echo.
    echo Contenu du fichier my.ini:
    type "C:\xampp\mysql\bin\my.ini" | findstr "basedir datadir port"
) else (
    echo ERREUR: Fichier my.ini introuvable!
)
echo.

echo [5/5] Instructions pour la suite:
echo.
echo 1. Arretez MySQL dans XAMPP
echo 2. Ouvrez les logs MySQL dans XAMPP (bouton Logs)
echo 3. Verifiez les erreurs recentes
echo 4. Redemarrez MySQL depuis XAMPP
echo.
echo Si le probleme persiste:
echo - Consultez FIX_MYSQL.md pour plus de solutions
echo - Verifiez l'espace disque disponible
echo - Verifiez les permissions sur C:\xampp\mysql\data
echo.
pause
