<?php require __DIR__ . '/header.php'; ?>

<h1>Laboratório de Vulnerabilidades (SDLC)</h1>
<p>Aplicação <strong>intencionalmente insegura</strong> para demonstrar análise com
   OWASP ZAP, Semgrep, SonarQube, Trivy e afins. Cada página contém uma ou mais falhas,
   comentadas no código-fonte com a tag <code>[VULN: ...]</code>.</p>

<table class="map">
    <tr><th>Vulnerabilidade</th><th>Onde</th><th>Como testar</th></tr>
    <tr><td>XSS Refletido</td><td>search.php</td><td><code>?q=&lt;script&gt;alert(1)&lt;/script&gt;</code></td></tr>
    <tr><td>XSS Stored</td><td>guestbook.php</td><td>Postar <code>&lt;script&gt;alert(1)&lt;/script&gt;</code></td></tr>
    <tr><td>SQL Injection</td><td>login.php, search.php</td><td><code>usuário: admin' --</code></td></tr>
    <tr><td>Information Disclosure</td><td>debug.php, erros</td><td>Abrir <code>debug.php</code></td></tr>
    <tr><td>Libs vulneráveis</td><td>header.php, package.json</td><td>jQuery 1.7.2</td></tr>
    <tr><td>Secrets hardcoded</td><td>config.php</td><td>Chaves/senhas no código</td></tr>
    <tr><td>Sessão insegura</td><td>header.php</td><td>Cookie sem HttpOnly/Secure</td></tr>
    <tr><td>HTTP inseguro</td><td>global</td><td>Sem TLS/HSTS</td></tr>
    <tr><td>IDOR</td><td>profile.php</td><td>Trocar <code>?id=1</code> por <code>?id=2</code></td></tr>
    <tr><td>Rate limit ausente</td><td>login.php</td><td>Brute force sem bloqueio</td></tr>
    <tr><td>Base64 "cifrando"</td><td>config.php, app.js</td><td>Token em base64</td></tr>
</table>

<?php require __DIR__ . '/footer.php'; ?>
