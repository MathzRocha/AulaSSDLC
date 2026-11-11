<?php
/**
 * ============================================================================
 *  CONEXÃO + BOOTSTRAP DO BANCO (SQLite, zero configuração)
 * ============================================================================
 *  Na primeira requisição cria o banco e popula dados de exemplo.
 *  SQLite é usado para o lab rodar sem instalar MySQL.
 * ----------------------------------------------------------------------------
 */
require_once __DIR__ . '/config.php';

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        // [VULN: Information Disclosure] Em erro, a exceção com caminho/driver
        // é exibida na tela porque APP_DEBUG=true.
        $pdo = new PDO('sqlite:' . DB_FILE);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        bootstrap_schema($pdo);
    }
    return $pdo;
}

function bootstrap_schema(PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT, password TEXT, email TEXT,
        role TEXT DEFAULT 'user', ssn TEXT
    )");
    $pdo->exec("CREATE TABLE IF NOT EXISTS comments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        author TEXT, body TEXT, created_at TEXT
    )");

    $count = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($count === 0) {
        // [VULN: Secrets / senhas fracas em texto puro] Sem hash de senha.
        $pdo->exec("INSERT INTO users (username,password,email,role,ssn) VALUES
            ('admin','admin123','admin@lab.local','admin','123.456.789-00'),
            ('alice','alice2024','alice@lab.local','user','111.222.333-44'),
            ('bob','qwerty','bob@lab.local','user','555.666.777-88')");
        $pdo->exec("INSERT INTO comments (author,body,created_at) VALUES
            ('sistema','Bem-vindo ao mural! Deixe seu comentário.','2024-01-01 09:00:00')");
    }
}
