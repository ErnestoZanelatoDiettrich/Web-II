<?php
require_once __DIR__ . '/../../config/database.php';

class Usuario
{
    public static function buscarPorEmail(string $email): ?array
    {
        $pdo = conectarBanco();
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();
        return $usuario ?: null;
    }

    public static function buscarPorId(int $id): ?array
    {
        $pdo = conectarBanco();
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $usuario = $stmt->fetch();
        return $usuario ?: null;
    }

    public static function criar(string $nome, string $email, string $senha, string $tipo = 'usuario'): int
    {
        $pdo = conectarBanco();
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
            'INSERT INTO usuarios (nome, email, senha_hash, tipo, criado_em) VALUES (:nome, :email, :senha, :tipo, NOW())'
        );
        $stmt->execute([
            'nome'  => $nome,
            'email' => $email,
            'senha' => $hash,
            'tipo'  => $tipo,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function atualizarSenha(int $usuarioId, string $novaSenha): void
    {
        $pdo = conectarBanco();
        $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE usuarios SET senha_hash = :senha WHERE id = :id');
        $stmt->execute(['senha' => $hash, 'id' => $usuarioId]);
    }

    public static function salvarTokenReset(int $usuarioId, string $token, string $expiraEm): void
    {
        $pdo = conectarBanco();
        // Remove tokens antigos do mesmo usuário
        $pdo->prepare('DELETE FROM redefinicoes_senha WHERE usuario_id = :id')->execute(['id' => $usuarioId]);

        $stmt = $pdo->prepare(
            'INSERT INTO redefinicoes_senha (usuario_id, token, expira_em) VALUES (:id, :token, :expira)'
        );
        $stmt->execute(['id' => $usuarioId, 'token' => $token, 'expira' => $expiraEm]);
    }

    public static function buscarPorTokenReset(string $token): ?array
    {
        $pdo = conectarBanco();
        $stmt = $pdo->prepare(
            'SELECT r.usuario_id, r.expira_em FROM redefinicoes_senha r WHERE r.token = :token LIMIT 1'
        );
        $stmt->execute(['token' => $token]);
        $resultado = $stmt->fetch();
        return $resultado ?: null;
    }

    public static function removerTokenReset(string $token): void
    {
        $pdo = conectarBanco();
        $pdo->prepare('DELETE FROM redefinicoes_senha WHERE token = :token')->execute(['token' => $token]);
    }
}
