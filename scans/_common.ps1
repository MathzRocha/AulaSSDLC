# ----------------------------------------------------------------------------
#  Funções e variáveis comuns aos scripts scan-*.ps1 (carregado com ". _common.ps1")
#  Não precisa rodar este arquivo diretamente.
# ----------------------------------------------------------------------------
$ErrorActionPreference = 'Continue'
$Lab    = Split-Path $PSScriptRoot -Parent
# 127.0.0.1 (IPv4) e não "localhost": no Windows o php -S localhost escuta só em ::1
# e os scanners que rodam em Docker (Nikto) não alcançam o app.
$AppUrl = 'http://127.0.0.1:8000'
Set-Location $Lab

# Garante PATH e variáveis atualizados mesmo em terminais abertos antes da instalação
$env:Path = [Environment]::GetEnvironmentVariable('Path','Machine') + ';' + [Environment]::GetEnvironmentVariable('Path','User')
foreach ($v in 'JAVA_HOME','SONAR_TOKEN','SONAR_HOST_URL','NVD_API_KEY') {
    $val = [Environment]::GetEnvironmentVariable($v,'User')
    if ($val -and -not (Get-Item "env:$v" -EA SilentlyContinue)) { Set-Item "env:$v" $val }
}
$env:PYTHONUTF8 = '1'   # Semgrep no Windows

# Cria (e devolve) a pasta de relatórios da ferramenta: reports\<nome>
function Get-ReportDir([string]$Name) {
    $d = Join-Path $Lab "reports\$Name"
    New-Item -ItemType Directory -Force $d | Out-Null
    return $d
}

function Write-Title([string]$Text) { Write-Host "`n==== $Text ====" -ForegroundColor Cyan }
function Write-Done([string]$Path)  { Write-Host "`nRelatório(s) em: $Path" -ForegroundColor Green }

# Sobe o app com o servidor embutido do PHP se ele ainda não estiver no ar
function Start-LabApp {
    try { Invoke-WebRequest $AppUrl -UseBasicParsing -TimeoutSec 3 | Out-Null; return $true } catch {}
    Write-Host "App fora do ar - iniciando 'php -S 127.0.0.1:8000' numa janela minimizada..." -ForegroundColor Yellow
    Start-Process php -ArgumentList '-S','127.0.0.1:8000' -WorkingDirectory $Lab -WindowStyle Minimized
    Start-Sleep 3
    try { Invoke-WebRequest $AppUrl -UseBasicParsing -TimeoutSec 5 | Out-Null; return $true }
    catch { Write-Warning "Não consegui subir o app em $AppUrl"; return $false }
}
