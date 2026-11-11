# Secret Scanning - gitleaks
# Uso: .\scans\scan-gitleaks.ps1
# 'gitleaks dir' varre os arquivos (o lab não é repositório git).
# Num repositório git use: gitleaks git -v   (varre também o histórico de commits)
. "$PSScriptRoot\_common.ps1"
$out = Get-ReportDir 'gitleaks'
Write-Title 'gitleaks (secrets)'

gitleaks dir . -v --redact=0 --report-format json --report-path "$out\gitleaks.json"

Write-Done $out
