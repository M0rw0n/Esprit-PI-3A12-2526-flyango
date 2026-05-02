@echo off
chcp 65001 >nul
echo ==========================================
echo   Telechargement de Mercure Hub
echo ==========================================
echo.

powershell -Command "Invoke-WebRequest -Uri 'https://github.com/dunglas/mercure/releases/download/v0.22.1/mercure_0.22.1_windows_x86_64.zip' -OutFile 'mercure.zip'"
echo.
echo Extraction...
powershell -Command "Expand-Archive -Path 'mercure.zip' -DestinationPath '.' -Force"
del mercure.zip

echo.
echo ==========================================
echo   Demarrage de Mercure
echo ==========================================
echo.
echo Le hub Mercure demarre sur http://localhost:3000
echo Appuyez sur une touche pour arreter...
pause >nul

.\mercure.exe -jwtKey="!ChangeThisMercureHubJWTSecretKey!" -addr=:3000 -debug

pause