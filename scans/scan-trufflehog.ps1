# Secret Scanning - TruffleHog
# Uso: .\scans\scan-trufflehog.ps1
# Pastas ignoradas: scans\trufflehog-exclude.txt
. "$PSScriptRoot\_common.ps1"
$out = Get-ReportDir 'trufflehog'
Write-Title 'TruffleHog (secrets)'

trufflehog filesystem . --exclude-paths "$Lab\scans\trufflehog-exclude.txt" --no-update 2>&1 |
  Tee-Object "$out\trufflehog.txt"

Write-Done $out
