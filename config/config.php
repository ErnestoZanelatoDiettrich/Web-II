<?php
/**
 * Configurações gerais do Trustic
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('NOME_SITE', 'Trustic');
define('BASE_URL', '/');
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_CAPAS_DIR', ROOT_PATH . '/public/uploads/capas');
define('UPLOAD_AVATARES_DIR', ROOT_PATH . '/public/uploads/avatares');
define('UPLOAD_MAX_BYTES', 2 * 1024 * 1024); // 2MB
define('ITENS_POR_PAGINA', 9);

// ---------- Autenticação / Autorização ----------
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
        'id'         => $_SESSION['usuario_id'],
        'nome'       => $_SESSION['usuario_nome'],
        'email'      => $_SESSION['usuario_email'],
        'tipo'       => $_SESSION['usuario_tipo'],
        'avatar_url' => $_SESSION['usuario_avatar'] ?? null,
    ];
}

function ehCritico(): bool
{
    return usuarioLogado() && in_array($_SESSION['usuario_tipo'], ['critico', 'admin'], true);
}

function ehAdmin(): bool
{
    return usuarioLogado() && $_SESSION['usuario_tipo'] === 'admin';
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
    if (!ehCritico()) {
        header('Location: ' . BASE_URL . 'index.php?page=jogos');
        exit;
    }
}

function exigirAdmin(): void
{
    exigirLogin();
    if (!ehAdmin()) {
        header('Location: ' . BASE_URL . 'index.php?page=inicio');
        exit;
    }
}

// ---------- Segurança ----------
function e(?string $valor): string
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

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

// ---------- Utilitários ----------
function corNota($nota): string
{
    if ($nota === null) return 'nota-neutra';
    if ($nota >= 75) return 'nota-boa';
    if ($nota >= 50) return 'nota-mediana';
    return 'nota-ruim';
}

function redirecionar(string $destino): void
{
    header('Location: ' . $destino);
    exit;
}

function flashSucesso(string $mensagem): void
{
    $_SESSION['flash_sucesso'] = $mensagem;
}

function flashErro(string $mensagem): void
{
    $_SESSION['flash_erro'] = $mensagem;
}
