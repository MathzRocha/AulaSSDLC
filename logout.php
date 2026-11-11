<?php
require __DIR__ . '/header.php';
// [VULN: Gerenciamento inseguro de sessões] "logout" só limpa o array,
// NÃO destrói a sessão nem invalida o cookie (session_destroy ausente).
$_SESSION['user'] = null;
header('Location: index.php');
exit;
