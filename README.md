# 🔓 Lab Inseguro — Laboratório de Vulnerabilidades para SDLC

Aplicação **intencionalmente vulnerável** (PHP + HTML + CSS + JS) para a aula de
**SDLC / Segurança**. Serve para demonstrar, na prática, como ferramentas de
**SAST, DAST e SCA** encontram falhas e como fazer *code review* de segurança.

> ⚠️ **AVISO**: código vulnerável de propósito. Rode **apenas localmente**, em
> ambiente isolado. **Nunca** exponha na internet nem use em produção.

---

## ▶️ Como rodar (sem instalar nada além do PHP)

```bash
cd lab-inseguro
php -S 127.0.0.1:8000
```

Acesse: <http://127.0.0.1:8000>

> Use `127.0.0.1` em vez de `localhost`: no Windows, `localhost` faz o PHP escutar
> só em IPv6 (`::1`) e os scanners em Docker (Nikto, ZAP) não alcançam o app.

O banco **SQLite** é criado e populado automaticamente na primeira requisição
(`lab.sqlite`). Usuários de teste:

| usuário | senha    | papel |
|---------|----------|-------|
| admin   | admin123 | admin |
| alice   | alice2024| user  |
| bob     | qwerty   | user  |

### Alternativa com Docker (para scan de imagem com Trivy)

```bash
docker build -t lab-inseguro .
docker run -p 8000:80 lab-inseguro
```

---

## 🐞 Vulnerabilidades incluídas e onde encontrá-las

| # | Vulnerabilidade | Arquivo | Como demonstrar |
|---|-----------------|---------|-----------------|
| 1 | **XSS Refletido** | `search.php` | `search.php?q=<script>alert(1)</script>` |
| 2 | **XSS Stored** | `guestbook.php` | Publicar recado com `<script>alert(1)</script>` |
| 3 | **SQL Injection** | `login.php`, `search.php`, `profile.php` | Usuário `admin' --` / `?q=' UNION SELECT ...` |
| 4 | **Information Disclosure** | `debug.php`, `config.php`, erros, `footer.php` | Abrir `debug.php` |
| 5 | **Bibliotecas vulneráveis** | `header.php`, `package.json`, `composer.json` | jQuery 1.7.2, lodash 4.17.4, guzzle 6.2.0… |
| 6 | **Secrets/Tokens hardcoded** | `config.php`, `assets/app.js` | Chaves de API, senhas, JWT secret no código |
| 7 | **Sessão insegura** | `header.php`, `login.php`, `logout.php` | Cookie sem HttpOnly/Secure; sem regenerate; sem destroy |
| 8 | **Comunicação insegura (HTTP)** | global, `app.js` | Sem TLS/HSTS; API key na querystring |
| 9 | **IDOR** | `profile.php` | Trocar `?id=1` → `?id=2`, `?id=3` |
| 10 | **Rate limit ausente** | `login.php` | Brute force sem bloqueio |
| 11 | **Informação em base64** | `config.php`, `assets/app.js` | Token/credencial só codificada, não cifrada |

Cada falha está marcada no código com a tag `[VULN: ...]` em comentário.

---

## 🛠️ Ferramentas: qual pega o quê

O guia completo de ferramentas — agrupadas por categoria (SAST, DAST, SCA,
Secret Scanning, IaC/Container e Rede), com o **comando de cada uma** apontando
para este lab e a **matriz de cobertura** das 11 vulnerabilidades — está em
**[`tools.md`](tools.md)**.

Resumo rápido por categoria:

| Categoria | Ferramentas | Exemplo |
|---|---|---|
| **SAST** | Semgrep, SonarQube | `semgrep --config=auto .` |
| **DAST** | OWASP ZAP, Burp Suite, Nikto | `zap-full-scan.py -t http://host.docker.internal:8000` |
| **SCA** | Trivy, Dependency-Check, OSV-Scanner, retire.js, npm/composer audit | `trivy fs .` |
| **Secret Scanning** | gitleaks, trufflehog | `gitleaks detect --source . -v` |
| **IaC/Container** | Hadolint, Trivy config | `hadolint Dockerfile` |
| **Rede** | Wireshark | capturar com filtro `http` |

> 👉 Comandos completos e a matriz vulnerabilidade × ferramenta: ver **`tools.md`**.

---

## 🎯 Sugestão de roteiro de aula

1. **Rodar o app** e navegar pelas páginas (ver `index.php` com o mapa).
2. **Explorar manualmente** 2–3 falhas (SQLi no login, XSS na busca, IDOR no perfil).
3. **SAST**: rodar Semgrep/SonarQube e comparar os achados com o que viram à mão.
4. **SCA**: rodar Trivy/retire.js e mostrar as CVEs das dependências.
5. **DAST**: rodar OWASP ZAP com o app no ar e correlacionar os alertas.
6. **Discussão**: por que cada ferramenta pega (ou não) cada classe, e onde
   entram no pipeline de CI/CD (shift-left).

---

## ✅ Como seria a correção (para contrastar em aula)

- **SQLi** → *prepared statements* (`$pdo->prepare(...)->execute([...])`).
- **XSS** → `htmlspecialchars($x, ENT_QUOTES, 'UTF-8')` na saída; CSP.
- **Secrets** → variáveis de ambiente / cofre (Vault), nunca no repositório.
- **Sessão** → cookie `HttpOnly`, `Secure`, `SameSite=Lax`, `session_regenerate_id(true)`.
- **HTTP** → forçar HTTPS + header HSTS.
- **IDOR** → checar se o recurso pertence ao usuário autenticado.
- **Rate limit** → limitar tentativas por IP/usuário + backoff/captcha.
- **Base64** → não é criptografia; usar hashing/cifragem real.
- **Dependências** → atualizar e monitorar com SCA no CI.
