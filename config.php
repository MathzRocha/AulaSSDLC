<?php
/**
 * ============================================================================
 *  LAB INSEGURO - ARQUIVO DE CONFIGURAÇÃO
 * ============================================================================
 *  ATENÇÃO: Código intencionalmente vulnerável para fins DIDÁTICOS.
 *  NÃO usar em produção. Rodar SOMENTE em ambiente local isolado.
 * ============================================================================
 *
 *  >>> VULN: Tokens e Secrets Hardcoded
 *  Detectado por: Semgrep, SonarQube, Trufflehog, gitleaks
 * ----------------------------------------------------------------------------
 */

// [VULN: Secrets Hardcoded] Credenciais e chaves escritas diretamente no código.
define('DB_FILE', __DIR__ . '/lab.sqlite');
define('DB_USER', 'admin');
define('DB_PASS', 'SuperSecret123!');                       // senha hardcoded
define('API_KEY', 'sk_live_51H8xY2eZvKflkjasd09182job');   // chave de API hardcoded
define('AWS_SECRET_ACCESS_KEY', 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY');
define('JWT_SECRET', 'meu-segredo-jwt-123');                // segredo JWT fraco e fixo

// [VULN: Informações Encodadas em base64] "esconder" segredo apenas codificando.
// base64_decode -> 'token_admin_mestre_2024'
define('ADMIN_TOKEN_B64', 'dG9rZW5fYWRtaW5fbWVzdHJlXzIwMjQ=');

// [VULN: Information Disclosure] Modo debug ligado expõe erros detalhados.
define('APP_DEBUG', true);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// [VULN: Comunicações inseguras HTTP] App assume HTTP puro, sem forçar HTTPS/HSTS.
define('BASE_URL', 'http://localhost:8000');
