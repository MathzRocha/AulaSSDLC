# SCA - retire.js: bibliotecas JavaScript vulneráveis
# Uso: .\scans\scan-retire.ps1
# O retire.js analisa os arquivos JS instalados. Como o jQuery vem do CDN, o script
# instala as dependências do package.json em node_modules (sem rodar scripts de instalação).
. "$PSScriptRoot\_common.ps1"
$out = Get-ReportDir 'retire'
Write-Title 'retire.js (bibliotecas JS)'

if (-not (Test-Path "$Lab\node_modules")) {
    Write-Host 'Instalando dependências JS (npm ci --ignore-scripts)...' -ForegroundColor Yellow
    npm ci --ignore-scripts --no-audit --no-fund
}

retire --path . --outputformat text --outputpath "$out\retire.txt"
retire --path . --outputformat json --outputpath "$out\retire.json"
Get-Content "$out\retire.txt"

Write-Done $out
