<?php
/**
 * Configurações gerais do sistema CriticaJá
 */

// Sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Exibição de erros (desativar em produção)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// URL base do sistema (ajuste conforme o ambiente)
define('BASE_URL', '/');

// Caminho absoluto do projeto
define('ROOT_PATH', dirname(__DIR__));

// Helpers de sessão / autenticação
function usuarioLogado(): bool
{
    return isset($_SESSION['usuario_id']);
}

function usuarioAtual(): ?array
{
    if (!usuarioLogado()) {
        return null;
    }
    return [
        'id'    => $_SESSION['usuario_id'],
        'nome'  => $_SESSION['usuario_nome'],
        'email' => $_SESSION['usuario_email'],
        'tipo'  => $_SESSION['usuario_tipo'],
    ];
}

function exigirLogin(): void
{
    if (!usuarioLogado()) {
        header('Location: ' . BASE_URL . 'index.php?page=login');
        exit;
    }
}

function exigirCritico(): void
{
    exigirLogin();
    if ($_SESSION['usuario_tipo'] !== 'critico') {
        header('Location: ' . BASE_URL . 'index.php?page=jogos');
        exit;
    }
}

// Proteção básica contra XSS ao imprimir dados na tela
function e(?string $valor): string
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

// Token CSRF simples
function gerarTokenCsrf(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validarTokenCsrf(?string $token): bool
{
    return isset($_SESSION['csrf_token']) && $token !== null && hash_equals($_SESSION['csrf_token'], $token);
}
