<?php
require_once __DIR__ . '/db.php';

/**
 * [VULN: Gerenciamento inseguro de sessões]
 * - Cookie de sessão SEM HttpOnly e SEM Secure (acessível via JS / enviado em HTTP).
 * - Sem SameSite (exposto a CSRF).
 * - session id não é regenerado após login (fixação de sessão).
 */
session_set_cookie_params([
    'lifetime' => 0,
    'httponly' => false,   // deveria ser true
    'secure'   => false,   // deveria ser true (HTTPS)
    'samesite' => 'None',  // deveria ser Lax/Strict
]);
session_start();

// [VULN: Comunicações inseguras HTTP] Nenhum redirect para HTTPS, nenhum header HSTS.
?><!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lab Inseguro - SDLC</title>
    <!-- [VULN: Bibliotecas com vulnerabilidades] jQuery 1.7.2 (CVE-2011-4969,
         CVE-2015-9251, XSS). Detectável por OWASP ZAP (passive scan) e por
         retire.js / Trivy no package.json. -->
    <script src="https://code.jquery.com/jquery-1.7.2.min.js"></script>
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/app.js" defer></script>
</head>
<body>
<header class="topbar">
    <a class="brand" href="index.php">🔓 Lab Inseguro</a>
    <nav>
        <a href="search.php">Busca</a>
        <a href="guestbook.php">Mural</a>
        <a href="profile.php?id=1">Perfil</a>
        <a href="debug.php">Debug</a>
        <?php if (!empty($_SESSION['user'])): ?>
            <span class="who">olá, <?= $_SESSION['user']['username'] ?> (<a href="logout.php">sair</a>)</span>
        <?php else: ?>
            <a href="login.php">Entrar</a>
        <?php endif; ?>
    </nav>
</header>
<main class="container">
