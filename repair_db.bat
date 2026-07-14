@echo off
echo Reparation de la base de donnees domini...
echo.

C:\xampp\mysql\bin\mysql.exe -u root -e "USE domini; DROP TABLE IF EXISTS migrations;"
C:\xampp\mysql\bin\mysql.exe -u root -e "USE domini; DROP TABLE IF EXISTS cache;"
C:\xampp\mysql\bin\mysql.exe -u root -e "USE domini; DROP TABLE IF EXISTS cache_locks;"

echo.
echo Suppression des fichiers .ibd restants...
if exist "C:\xampp\mysql\data\domini\migrations.ibd" del /F "C:\xampp\mysql\data\domini\migrations.ibd"
if exist "C:\xampp\mysql\data\domini\cache.ibd" del /F "C:\xampp\mysql\data\domini\cache.ibd"

echo.
echo Lancement des migrations...
php artisan migrate --force

echo.
echo Test de l'API...
curl http://localhost:8000/api/menu/categories

echo.
pause
