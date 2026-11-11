<?php require __DIR__ . '/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $author = $_POST['author'] ?? 'anônimo';
    $body   = $_POST['body'] ?? '';

    /**
     * [VULN: XSS Stored] + [VULN: SQL Injection]
     * O conteúdo é inserido sem prepared statement e, depois, exibido sem escape.
     * Payload <script>...</script> fica PERSISTIDO e executa para todos os visitantes.
     * Detectado por: OWASP ZAP (active scan), Semgrep, SonarQube.
     */
    $sql = "INSERT INTO comments (author, body, created_at)
            VALUES ('$author', '$body', '" . date('Y-m-d H:i:s') . "')";
    try {
        db()->exec($sql);
    } catch (Throwable $e) {
        echo '<p class="erro">Erro: ' . $e->getMessage() . '</p>';
    }
    header('Location: guestbook.php');
    exit;
}

$comentarios = db()->query("SELECT * FROM comments ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Mural de recados</h1>

<form method="post" class="card">
    <label>Nome <input name="author"></label>
    <label>Recado <textarea name="body"></textarea></label>
    <button>Publicar</button>
</form>

<div class="comments">
    <?php foreach ($comentarios as $c): ?>
        <div class="comment">
            <!-- [VULN: XSS Stored] autor e corpo renderizados sem htmlspecialchars() -->
            <strong><?= $c['author'] ?></strong>
            <span class="date"><?= $c['created_at'] ?></span>
            <p><?= $c['body'] ?></p>
        </div>
    <?php endforeach; ?>
</div>

<?php require __DIR__ . '/footer.php'; ?>
