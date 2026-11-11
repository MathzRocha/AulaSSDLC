<?php require __DIR__ . '/header.php';

$q = $_GET['q'] ?? '';
$resultados = [];

if ($q !== '') {
    /**
     * [VULN: SQL Injection] Busca também concatena a entrada na query.
     * Ex.: ?q=' UNION SELECT id,username,password,email,role,ssn FROM users --
     */
    $sql = "SELECT username, email FROM users WHERE username LIKE '%$q%'";
    try {
        $resultados = db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        echo '<p class="erro">Erro: ' . $e->getMessage() . '</p>'; // Information Disclosure
    }
}
?>

<h1>Busca de usuários</h1>

<form method="get" class="card">
    <input name="q" value="<?= $_GET['q'] ?? '' /* [VULN: XSS Refletido] atributo sem escape */ ?>">
    <button>Buscar</button>
</form>

<?php if ($q !== ''): ?>
    <!--
      [VULN: XSS Refletido]
      O termo buscado é ecoado no HTML SEM htmlspecialchars().
      Teste: ?q=<script>alert(document.cookie)</script>
      Detectado por: OWASP ZAP (active scan), Semgrep, SonarQube.
    -->
    <p>Resultados para: <strong><?= $q ?></strong></p>

    <ul class="list">
        <?php foreach ($resultados as $r): ?>
            <li><?= $r['username'] ?> — <?= $r['email'] ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php require __DIR__ . '/footer.php'; ?>
