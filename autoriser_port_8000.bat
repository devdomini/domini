@echo off
echo ========================================
echo Autorisation du port 8000 dans le pare-feu
echo ========================================
echo.

REM Vérifier si on est administrateur
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ERREUR: Ce script doit etre execute en tant qu'administrateur!
    echo.
    echo Clic droit sur ce fichier -^> "Executer en tant qu'administrateur"
    pause
    exit /b 1
)

echo Ajout de la regle de pare-feu pour le port 8000...
netsh advfirewall firewall add rule name="Laravel API Port 8000" dir=in action=allow protocol=TCP localport=8000

if %errorLevel% equ 0 (
    echo.
    echo SUCCES: Le port 8000 est maintenant autorise dans le pare-feu!
    echo.
) else (
    echo.
    echo ERREUR: Impossible d'ajouter la regle de pare-feu.
    echo.
)

pause
