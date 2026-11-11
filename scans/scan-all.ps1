# Roda TODOS os scans em sequência (chama cada scan-*.ps1 desta pasta)
# Uso: .\scans\scan-all.ps1
$order = 'semgrep','sonarqube','trivy','osv-scanner','dependency-check','retire',
         'npm-audit','composer-audit','gitleaks','trufflehog','hadolint','nikto'
foreach ($t in $order) { & "$PSScriptRoot\scan-$t.ps1" }
