# DAST - Nikto: scanner de servidor web (precisa do app rodando)
# Uso: .\scans\scan-nikto.ps1
# Obs.: o Nikto roda em Docker (o Defender bloqueia o nikto.pl nativo como HackTool).
#       O wrapper 'nikto' troca 127.0.0.1/localhost por host.docker.internal sozinho.
. "$PSScriptRoot\_common.ps1"
Write-Title 'Nikto (DAST)'
if (-not (Start-LabApp)) { exit 1 }
$out = Get-ReportDir 'nikto'

# -timeout 30: o servidor embutido do PHP atende uma requisição por vez
# -o deve ser caminho relativo ao lab (a pasta é montada dentro do container)
nikto -h $AppUrl -timeout 30 -maxtime 20m -o reports/nikto/nikto.htm -Format htm

Write-Done $out
