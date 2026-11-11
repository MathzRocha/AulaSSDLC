<?php require __DIR__ . '/header.php';

$erro = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    /**
     * [VULN: Rate Limit ausente]
     * Nenhum controle de tentativas, bloqueio, captcha ou atraso.
     * Permite brute force / credential stuffing ilimitado.
     * Detectado por: OWASP ZAP (forced browse / fuzzer), revisão manual.
     */

    /**
     * [VULN: SQL Injection]
     * Entrada concatenada diretamente na query, sem prepared statement.
     * Bypass clássico:  username = admin' --   (senha ignorada)
     * Detectado por: Semgrep, SonarQube (taint analysis) e OWASP ZAP (active scan).
     */
    $sql = "SELECT * FROM users WHERE username = '$user' AND password = '$pass'";

    try {
        $row = db()->query($sql)->fetch(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        // [VULN: Information Disclosure] Erro de SQL devolvido ao usuário.
        $erro = 'Erro de SQL: ' . $e->getMessage() . '<br><code>' . $sql . '</code>';
        $row = null;
    }

    if ($row) {
        /**
         * [VULN: Gerenciamento inseguro de sessões]
         * session_id NÃO é regenerado após autenticar -> fixação de sessão.
         * O correto seria session_regenerate_id(true) aqui.
         */
        $_SESSION['user'] = $row;
        header('Location: index.php');
        exit;
    } elseif (!$erro) {
        $erro = 'Credenciais inválidas.';
    }
}
?>

<h1>Entrar</h1>
<?php if ($erro): ?><p class="erro"><?= $erro /* já contém HTML de debug */ ?></p><?php endif; ?>

<!-- [VULN: Comunicações inseguras HTTP] Form enviado por HTTP em texto puro. -->
<form method="post" action="login.php" class="card">
    <label>Usuário <input name="username" autofocus></label>
    <label>Senha <input name="password" type="password"></label>
    <button type="submit">Login</button>
    <!-- <p class="hint">Dica didática: <code>admin / admin123</code> — ou tente <code>admin' --</code></p> -->
</form>

<?php require __DIR__ . '/footer.php'; ?>
