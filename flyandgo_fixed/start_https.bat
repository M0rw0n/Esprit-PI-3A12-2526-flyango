@echo off
cd /d "%~dp0"

if not exist "config\certs\server.crt" (
    echo Generez les certificats d'abord:
    php generate_cert.php
)

echo ========================================
echo    Fly&Go - Serveur HTTPS
echo ========================================
echo.
echo URL: https://localhost:8443
echo.
echo Acceptez le certificat auto-signe dans le navigateur!
echo Ctrl+C pour arreter
echo ========================================

php -S localhost:8443 -t public router_ssl.php