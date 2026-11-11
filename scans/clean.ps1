# Limpa os resultados gerados pelos scans e deixa o lab pronto para a próxima aula
# Uso: .\scans\clean.ps1
$Lab = Split-Path $PSScriptRoot -Parent

Get-Process php -EA SilentlyContinue | Stop-Process -Force   # para o servidor do lab, se estiver rodando
foreach ($p in 'reports','node_modules','.scannerwork','vendor') {
    $f = Join-Path $Lab $p
    if (Test-Path $f) { Remove-Item -Recurse -Force $f; Write-Host "removido: $p" }
}
# Banco do app: é recriado com os dados iniciais na primeira requisição
# (apagar descarta recados/XSS que alunos ou o Nikto tenham gravado no mural)
$db = Join-Path $Lab 'lab.sqlite'
if (Test-Path $db) { Remove-Item -Force $db; Write-Host 'removido: lab.sqlite (será recriado)' }

Write-Host 'Lab limpo.' -ForegroundColor Green
