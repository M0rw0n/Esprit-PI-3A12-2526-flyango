$projectDir = "C:\Users\MSI\Downloads\flyandgo_fixed (1)\flyandgo_fixed"

Set-Location $projectDir

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   Fly&Go - Demarrage HTTPS Server" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$certsDir = Join-Path $projectDir "config\certs"
$certFile = Join-Path $certsDir "server.crt"
$keyFile = Join-Path $certsDir "server.key"

if (-not (Test-Path $certsDir)) {
    New-Item -ItemType Directory -Path $certsDir -Force | Out-Null
}

if (-not (Test-Path $certFile)) {
    Write-Host "Creation des certificats SSL..." -ForegroundColor Yellow

    $openssl = Get-Command openssl -ErrorAction SilentlyContinue
    if ($openssl) {
        openssl req -x509 -newkey rsa:4096 -keyout $keyFile -out $certFile -days 365 -nodes -subj "/CN=localhost" -addext "subjectAltName=DNS:localhost,IP:127.0.0.1"
        Write-Host "Certificats crees!" -ForegroundColor Green
    } else {
        Write-Host "OpenSSL non trouve. Utilisation du serveur dev avec HTTP..." -ForegroundColor Red
        Write-Host ""
        Write-Host "Demarrage sur http://localhost:8000" -ForegroundColor Cyan
        php -S localhost:8000 -t public
        exit
    }
}

Write-Host ""
Write-Host "Demarrage du serveur HTTPS sur https://localhost:8443" -ForegroundColor Cyan
Write-Host "Cliquez sur 'Avance' puis 'Continuer vers localhost' dans le navigateur" -ForegroundColor Yellow
Write-Host "Appuyez sur Ctrl+C pour arreter" -ForegroundColor Gray
Write-Host ""

php -S localhost:8443 -t public