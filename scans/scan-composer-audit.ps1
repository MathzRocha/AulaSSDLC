# SCA - composer audit: auditoria nativa do Composer sobre composer.lock
# Uso: .\scans\scan-composer-audit.ps1
. "$PSScriptRoot\_common.ps1"
$out = Get-ReportDir 'composer-audit'
Write-Title 'composer audit (SCA nativo)'

composer audit --locked --no-interaction 2>&1 | Tee-Object "$out\composer-audit.txt"
composer audit --locked --no-interaction --format=json 2>$null | Set-Content "$out\composer-audit.json"

Write-Done $out
