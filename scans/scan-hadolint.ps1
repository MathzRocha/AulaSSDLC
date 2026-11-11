# IaC / Container - Hadolint: lint de boas práticas do Dockerfile
# Uso: .\scans\scan-hadolint.ps1
# Obs.: root implícito e 'chmod 777' não estão nas regras do Hadolint
#       (esses o Trivy config detecta - ver scan-trivy.ps1).
. "$PSScriptRoot\_common.ps1"
$out = Get-ReportDir 'hadolint'
Write-Title 'Hadolint (Dockerfile)'

hadolint Dockerfile 2>&1 | Tee-Object "$out\hadolint.txt"
if ($LASTEXITCODE -eq 0) {
    'Nenhum alerta do Hadolint neste Dockerfile.' | Tee-Object "$out\hadolint.txt" | Write-Host -ForegroundColor Green
}

Write-Done $out
