# SCA - npm audit: auditoria nativa do npm sobre package-lock.json
# Uso: .\scans\scan-npm-audit.ps1
. "$PSScriptRoot\_common.ps1"
$out = Get-ReportDir 'npm-audit'
Write-Title 'npm audit (SCA nativo)'

npm audit 2>&1 | Tee-Object "$out\npm-audit.txt"
npm audit --json 2>$null | Set-Content "$out\npm-audit.json"

Write-Done $out
