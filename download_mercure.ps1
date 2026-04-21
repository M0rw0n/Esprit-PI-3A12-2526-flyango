# Fly&Go - Download Mercure Hub
# Run this script as Administrator

$ErrorActionPreference = "Stop"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Downloading Mercure Hub for Windows" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$versionUrl = "https://api.github.com/repos/dunglas/mercure/releases/latest"
$downloadUrl = "https://github.com/dunglas/mercure/releases/latest/download/mercure_windows_amd64.zip"
$outputFile = "$PSScriptRoot\mercure.zip"

Write-Host "Downloading from GitHub..." -ForegroundColor Yellow
try {
    Invoke-WebRequest -Uri $downloadUrl -OutFile $outputFile -UseBasicParsing
    Write-Host "Download complete!" -ForegroundColor Green
} catch {
    Write-Host "Error downloading: $_" -ForegroundColor Red
    Write-Host "Trying alternative URL..."
    $altUrl = "https://github.com/dunglas/mercure/releases/download/v0.7.2/mercure_0.7.2_windows_amd64.zip"
    Invoke-WebRequest -Uri $altUrl -OutFile $outputFile -UseBasicParsing
}

Write-Host "Extracting..." -ForegroundColor Yellow
Expand-Archive -Path $outputFile -DestinationPath "$PSScriptRoot" -Force
Remove-Item $outputFile -Force

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "  Mercure downloaded successfully!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "To start Mercure, run:" -ForegroundColor Cyan
Write-Host "  .\mercure.exe -jwtKey='!ChangeThisMercureHubJWTSecretKey!' -addr=:3000" -ForegroundColor White
Write-Host ""
Write-Host "Or double-click start_mercure.bat" -ForegroundColor Yellow
Write-Host ""

# Create start script
@"
@echo off
echo Starting Mercure Hub...
start /B .\mercure.exe -jwtKey="!ChangeThisMercureHubJWTSecretKey!" -addr=:3000 -debug
echo Mercure running on http://localhost:3000
pause
"@ | Out-File -FilePath "$PSScriptRoot\start_mercure.bat" -Encoding ASCII

Write-Host "Done!" -ForegroundColor Green