# Ferramentas utilizadas no Lab (SDLC)

Ferramentas agrupadas por categoria, com o comando apontando para **este lab**
(app em `http://127.0.0.1:8000`). Juntas, dão **100% de cobertura** das 11
vulnerabilidades presentes no laboratório.

> Rode o app antes dos scans de DAST/rede: `php -S 127.0.0.1:8000`
> (use `127.0.0.1`, não `localhost`: no Windows o `localhost` escuta só em IPv6 `::1`
> e os scanners que rodam em Docker — Nikto, ZAP — não conseguem alcançar o app)

---

## ⚡ Scripts prontos: um por ferramenta

Todas as ferramentas abaixo já estão instaladas e configuradas nesta máquina.
Cada uma tem um script próprio em `scans/` que roda **só aquela ferramenta**
contra o lab e grava o relatório em `reports/<ferramenta>/`. Rode a partir da
pasta do lab:

| Categoria | Script | Relatório |
|---|---|---|
| SAST | `.\scans\scan-semgrep.ps1` | `reports/semgrep/` (txt, json, sarif) |
| SAST | `.\scans\scan-sonarqube.ps1` | <http://localhost:9000/dashboard?id=lab-inseguro> |
| DAST | `.\scans\scan-nikto.ps1` (sobe o app sozinho) | `reports/nikto/nikto.htm` |
| SCA / IaC | `.\scans\scan-trivy.ps1` · `-Image` para escanear também a imagem | `reports/trivy/` |
| SCA | `.\scans\scan-dependency-check.ps1` | `reports/dependency-check/dependency-check-report.html` |
| SCA | `.\scans\scan-osv-scanner.ps1` | `reports/osv-scanner/` |
| SCA | `.\scans\scan-retire.ps1` (instala `node_modules` se faltar) | `reports/retire/` |
| SCA | `.\scans\scan-npm-audit.ps1` | `reports/npm-audit/` |
| SCA | `.\scans\scan-composer-audit.ps1` | `reports/composer-audit/` |
| Secrets | `.\scans\scan-gitleaks.ps1` | `reports/gitleaks/gitleaks.json` |
| Secrets | `.\scans\scan-trufflehog.ps1` | `reports/trufflehog/` |
| IaC | `.\scans\scan-hadolint.ps1` | `reports/hadolint/` |

Extras:

```powershell
.\scans\scan-all.ps1     # roda todos em sequência
.\scans\clean.ps1        # apaga reports/, node_modules/, para o PHP e reseta o lab.sqlite
```

**SonarQube:** login `admin` / `LabSdlc@2026!` (o token de análise está na
variável de usuário `SONAR_TOKEN`). Para parar o servidor:
`docker compose -f scans/sonarqube/docker-compose.yml stop`.

Arquivos de configuração usados pelas ferramentas: `sonar-project.properties`,
`.semgrepignore`, `.gitleaks.toml`, `trivy.yaml`, `scans/trufflehog-exclude.txt`,
`scans/sonarqube/docker-compose.yml`.

---

## 🔎 SAST — Análise estática do código-fonte

| Ferramenta | Comando | Pega no lab |
|---|---|---|
| **Semgrep** | `semgrep scan --config p/php --config p/javascript --config p/secrets --config p/owasp-top-ten` (ou `semgrep --config=auto .`) | SQLi, XSS refletido/stored, secrets, `phpinfo()`, base64 |
| **SonarQube** | Servidor: `docker compose -f scans/sonarqube/docker-compose.yml up -d` → <http://localhost:9000> · Scan: `docker run --rm -e SONAR_HOST_URL=http://host.docker.internal:9000 -e SONAR_TOKEN=$env:SONAR_TOKEN -v "${PWD}:/usr/src" sonarsource/sonar-scanner-cli` | SQLi, XSS, secrets, code smells, qualidade |

---

## 🌐 DAST — Análise dinâmica com o app rodando

| Ferramenta | Comando | Pega no lab |
|---|---|---|
| **OWASP ZAP** | `docker run -t ghcr.io/zaproxy/zaproxy zap-full-scan.py -t http://host.docker.internal:8000` | XSS, SQLi, cookie sem HttpOnly/Secure, headers ausentes, info disclosure, lib JS antiga |
| **Burp Suite** | GUI → configurar proxy e navegar o app | IDOR (`?id=`), brute force / rate limit no login, repetição de requisições |
| **Nikto** | `nikto -h http://127.0.0.1:8000 -timeout 30 -o reports/nikto.htm` (roda em Docker) | Information disclosure (`debug.php`, `phpinfo()`), arquivos expostos |

---

## 📦 SCA — Dependências e componentes vulneráveis

| Ferramenta | Comando | Pega no lab |
|---|---|---|
| **Trivy** | `trivy fs .` · `trivy image lab-inseguro` · `trivy config .` | Libs PHP/JS vulneráveis, CVEs da imagem, misconfig do Dockerfile, secrets |
| **OWASP Dependency-Check** | `dependency-check --project lab-inseguro --scan . --format HTML --out reports/dependency-check --nvdApiKey $env:NVD_API_KEY` (exige API key da NVD) | Dependências vulneráveis (base NVD/CVE) |
| **OSV-Scanner** | `osv-scanner scan --recursive .` | Dependências vulneráveis (base OSV) — compara achados com Trivy/DC |
| **retire.js** | `retire --path .` | Bibliotecas JS vulneráveis (jQuery 1.7.2 etc.) |
| **npm audit** | `npm audit` (o `package-lock.json` já foi gerado) | SCA nativo do `package.json` |
| **composer audit** | `composer audit --locked` (o `composer.lock` já foi gerado) | SCA nativo do `composer.json` |

---

## 🔑 Secret Scanning — Tokens e credenciais no código

| Ferramenta | Comando | Pega no lab |
|---|---|---|
| **gitleaks** | `gitleaks dir . -v` (a pasta não é repo git; em repo use `gitleaks git -v`) | Secrets/tokens hardcoded (`config.php`, `assets/app.js`) |
| **trufflehog** | `trufflehog filesystem . --exclude-paths scans/trufflehog-exclude.txt` | Secrets hardcoded + base64 de credenciais |

---

## 🐳 IaC / Container — Infraestrutura como código

| Ferramenta | Comando | Pega no lab |
|---|---|---|
| **Hadolint** | `hadolint Dockerfile` | Dockerfile inseguro (root, `chmod 777`, base antiga) |
| **Trivy config** | `trivy config .` | Misconfiguração de container/IaC |

---

## 📡 Rede — Comunicação insegura

| Ferramenta | Comando | Pega no lab |
|---|---|---|
| **Wireshark** | Capturar na interface local, filtro `http` | Login em HTTP texto puro, API key na querystring, cookie trafegando sem TLS |

---

## ✅ Matriz de cobertura (11 vulnerabilidades × ferramentas)

| # | Vulnerabilidade | Ferramenta(s) que detecta(m) |
|---|---|---|
| 1 | XSS Refletido | Semgrep, SonarQube, OWASP ZAP |
| 2 | XSS Stored | Semgrep, SonarQube, OWASP ZAP |
| 3 | SQL Injection | Semgrep, SonarQube, OWASP ZAP, Burp |
| 4 | Information Disclosure | OWASP ZAP, Nikto, Semgrep, revisão manual |
| 5 | Bibliotecas vulneráveis | Trivy, Dependency-Check, OSV-Scanner, retire.js, npm/composer audit, ZAP |
| 6 | Secrets/Tokens hardcoded | gitleaks, trufflehog, Semgrep, SonarQube, Trivy |
| 7 | Sessão insegura | OWASP ZAP, Burp |
| 8 | Comunicação insegura (HTTP) | Wireshark, OWASP ZAP, Burp |
| 9 | IDOR | Burp, OWASP ZAP, revisão manual |
| 10 | Rate limit ausente | Burp (fuzzer), OWASP ZAP |
| 11 | Informação em base64 | Semgrep, trufflehog, revisão manual |

---

## 🧩 Onde entram no pipeline (shift-left)

- **Pre-commit / IDE:** gitleaks, Semgrep
- **CI (build):** Semgrep, SonarQube, Trivy, Dependency-Check, OSV-Scanner, retire.js, Hadolint
- **CD / staging:** OWASP ZAP, Nikto, Burp
- **Análise de tráfego / troubleshooting:** Wireshark
