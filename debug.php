<?php require __DIR__ . '/header.php'; ?>

<h1>Painel de Debug</h1>

<!--
  [VULN: Information Disclosure]
  Endpoint de debug sem autenticação expondo:
  - configuração interna, segredos, versões, variáveis de ambiente, phpinfo().
  Detectado por: OWASP ZAP (spider + alertas), revisão manual, Semgrep (phpinfo).
-->

<h2>Configuração da aplicação</h2>
<pre>
APP_DEBUG   = <?= var_export(APP_DEBUG, true) . "\n" ?>
BASE_URL    = <?= BASE_URL . "\n" ?>
DB_FILE     = <?= DB_FILE . "\n" ?>
DB_USER     = <?= DB_USER . "\n" ?>
DB_PASS     = <?= DB_PASS . "\n"   /* secret vazando */ ?>
API_KEY     = <?= API_KEY . "\n" ?>
JWT_SECRET  = <?= JWT_SECRET . "\n" ?>

<!-- [VULN: Base64] "token mestre" só codificado, decodificável trivialmente -->
ADMIN_TOKEN (base64) = <?= ADMIN_TOKEN_B64 . "\n" ?>
</pre>

<h2>Sessão atual</h2>
<pre><?= htmlspecialchars(print_r($_SESSION, true)) ?></pre>

<h2>Variáveis de ambiente do servidor</h2>
<pre><?= htmlspecialchars(print_r($_SERVER, true)) ?></pre>

<h2>phpinfo()</h2>
<?php phpinfo(); // expõe versão do PHP, módulos, caminhos, config ?>

<?php require __DIR__ . '/footer.php'; ?>
