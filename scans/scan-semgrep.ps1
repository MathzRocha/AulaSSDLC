# SAST - Semgrep: análise estática do código PHP/JS
# Uso: .\scans\scan-semgrep.ps1
. "$PSScriptRoot\_common.ps1"
$out = Get-ReportDir 'semgrep'
Write-Title 'Semgrep (SAST)'

semgrep scan `
  --config p/php --config p/javascript --config p/secrets --config p/owasp-top-ten --config p/dockerfile `
  --metrics off `
  --text-output  "$out\semgrep.txt" `
  --json-output  "$out\semgrep.json" `
  --sarif-output "$out\semgrep.sarif"

Write-Done $out
