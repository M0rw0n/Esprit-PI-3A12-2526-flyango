@echo off
cd /d "%~dp0"
echo ========================================
echo Fly&Go - HTTPS Server
echo ========================================
echo.
echo URL: https://localhost:8443
echo.
echo. Acceptez le certificat dans le navigateur!
echo.
echo Ctrl+C pour arreter
echo ========================================
php -S 0.0.0.0:8443 router_ssl.php