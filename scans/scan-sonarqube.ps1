# SAST - SonarQube Community (servidor em Docker, http://localhost:9000)
# Uso: .\scans\scan-sonarqube.ps1
# Login na interface: admin / LabSdlc@2026!   (token de análise em $env:SONAR_TOKEN)
# Configuração do projeto: sonar-project.properties
. "$PSScriptRoot\_common.ps1"
Write-Title 'SonarQube (SAST)'
if (-not $env:SONAR_TOKEN)    { Write-Warning 'Variável SONAR_TOKEN não definida.'; exit 1 }
if (-not $env:SONAR_HOST_URL) { $env:SONAR_HOST_URL = 'http://localhost:9000' }

# 1) Garante o servidor no ar
try { $s = (Invoke-RestMethod "$env:SONAR_HOST_URL/api/system/status").status } catch { $s = 'DOWN' }
if ($s -ne 'UP') {
    Write-Host 'Subindo o SonarQube (pode levar ~1 min)...' -ForegroundColor Yellow
    docker compose -f "$Lab\scans\sonarqube\docker-compose.yml" up -d
    do {
        Start-Sleep 5
        try { $s = (Invoke-RestMethod "$env:SONAR_HOST_URL/api/system/status").status } catch { $s = 'STARTING' }
        Write-Host "  status: $s"
    } while ($s -ne 'UP')
}

# 2) Roda o SonarScanner (em Docker) sobre a pasta do lab
docker run --rm `
  -e SONAR_HOST_URL=http://host.docker.internal:9000 `
  -e SONAR_TOKEN=$env:SONAR_TOKEN `
  --add-host=host.docker.internal:host-gateway `
  -v "${Lab}:/usr/src" `
  sonarsource/sonar-scanner-cli

# o scanner cria .scannerwork na pasta do lab - remove para não sujar o repositório
Remove-Item -Recurse -Force "$Lab\.scannerwork" -EA SilentlyContinue

Write-Host "`nResultados: http://localhost:9000/dashboard?id=lab-inseguro" -ForegroundColor Green
