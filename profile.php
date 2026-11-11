<?php require __DIR__ . '/header.php';

/**
 * [VULN: IDOR - Insecure Direct Object Reference]
 * Qualquer um informa ?id=N e vê o perfil de QUALQUER usuário, incluindo
 * dados sensíveis (e-mail, CPF/SSN, role). NÃO há verificação de que o
 * usuário logado só pode ver o próprio perfil.
 * Teste: profile.php?id=1, ?id=2, ?id=3 ...
 * Detectado por: revisão manual, OWASP ZAP (access control testing).
 */
$id = $_GET['id'] ?? '1';

// (também SQL Injection: id concatenado)
$sql = "SELECT id, username, email, role, ssn FROM users WHERE id = $id";
try {
    $u = db()->query($sql)->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    echo '<p class="erro">Erro: ' . $e->getMessage() . '</p>';
    $u = null;
}
?>

<h1>Perfil</h1>

<?php if ($u): ?>
    <div class="card">
        <p><strong>ID:</strong> <?= $u['id'] ?></p>
        <p><strong>Usuário:</strong> <?= $u['username'] ?></p>
        <!-- [VULN: Information Disclosure] dados sensíveis expostos sem autorização -->
        <p><strong>E-mail:</strong> <?= $u['email'] ?></p>
        <p><strong>CPF:</strong> <?= $u['ssn'] ?></p>
        <p><strong>Papel:</strong> <?= $u['role'] ?></p>
    </div>
<?php else: ?>
    <p>Usuário não encontrado.</p>
<?php endif; ?>

<?php require __DIR__ . '/footer.php'; ?>
