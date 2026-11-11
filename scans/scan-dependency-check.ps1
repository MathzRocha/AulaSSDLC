# SCA - OWASP Dependency-Check (base NVD)
# Uso: .\scans\scan-dependency-check.ps1
# Requer a variável NVD_API_KEY (já definida). A 1a execução baixa a base NVD (demorado).
. "$PSScriptRoot\_common.ps1"
$out = Get-ReportDir 'dependency-check'
Write-Title 'OWASP Dependency-Check (SCA)'
if (-not $env:NVD_API_KEY) { Write-Warning 'NVD_API_KEY não definida - a atualização da base NVD vai falhar.' }

dependency-check `
  --project lab-inseguro `
  --scan . `
  --exclude '**/reports/**' --exclude '**/scans/**' --exclude '**/node_modules/**' `
  --enableExperimental `
  --format HTML --format JSON `
  --out "$out" `
  --nvdApiKey $env:NVD_API_KEY

Write-Done "$out\dependency-check-report.html"
