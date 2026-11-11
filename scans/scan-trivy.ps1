# SCA / Secrets / IaC - Trivy
# Uso: .\scans\scan-trivy.ps1          (filesystem + config)
#      .\scans\scan-trivy.ps1 -Image   (também a imagem Docker lab-inseguro; faz o build se faltar)
# Configuração: trivy.yaml (pastas ignoradas e scanners ativos)
param([switch]$Image)
. "$PSScriptRoot\_common.ps1"
$out = Get-ReportDir 'trivy'

Write-Title 'Trivy fs (dependências + secrets + misconfig)'
trivy fs . --format table --output "$out\trivy-fs.txt"
trivy fs . --format json  --output "$out\trivy-fs.json"
Get-Content "$out\trivy-fs.txt"

Write-Title 'Trivy config (Dockerfile / IaC)'
trivy config . --output "$out\trivy-config.txt"
Get-Content "$out\trivy-config.txt"

if ($Image) {
    Write-Title 'Trivy image lab-inseguro'
    docker image inspect lab-inseguro *> $null
    if ($LASTEXITCODE -ne 0) { docker build -t lab-inseguro . }
    trivy image lab-inseguro --severity HIGH,CRITICAL --output "$out\trivy-image.txt"
    Get-Content "$out\trivy-image.txt"
}

Write-Done $out
