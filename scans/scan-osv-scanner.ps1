# SCA - OSV-Scanner (base OSV.dev) sobre package-lock.json e composer.lock
# Uso: .\scans\scan-osv-scanner.ps1
. "$PSScriptRoot\_common.ps1"
$out = Get-ReportDir 'osv-scanner'
Write-Title 'OSV-Scanner (SCA)'

osv-scanner scan source -r . --format table --output-file "$out\osv-scanner.txt"
osv-scanner scan source -r . --format json  --output-file "$out\osv-scanner.json" *> $null
Get-Content "$out\osv-scanner.txt"

Write-Done $out
