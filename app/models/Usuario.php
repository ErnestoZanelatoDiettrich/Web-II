<?php
require_once __DIR__ . '/../../config/database.php';

class Usuario
{
    public static function buscarPorEmail(string $email): ?array
    {
        $pdo = conectarBanco();
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public static function buscarPorId(int $id): ?array
    {
        $pdo = conectarBanco();
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function criar(string $nome, string $email, string $senha, string $tipo = 'usuario'): int
    {
        $pdo = conectarBanco();
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
            'INSERT INTO usuarios (nome, email, senha_hash, tipo, criado_em) VALUES (:nome, :email, :senha, :tipo, NOW())'
        );
        $stmt->execute(['nome' => $nome, 'email' => $email, 'senha' => $hash, 'tipo' => $tipo]);
        return (int) $pdo->lastInsertId();
    }

    public static function atualizarPerfil(int $id, string $nome, string $bio, ?string $avatarArquivo): void
    {
        $pdo = conectarBanco();
        if ($avatarArquivo !== null) {
            $stmt = $pdo->prepare('UPDATE usuarios SET nome = :nome, bio = :bio, avatar_url = :avatar WHERE id = :id');
            $stmt->execute(['nome' => $nome, 'bio' => $bio, 'avatar' => $avatarArquivo, 'id' => $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE usuarios SET nome = :nome, bio = :bio WHERE id = :id');
            $stmt->execute(['nome' => $nome, 'bio' => $bio, 'id' => $id]);
        }
    }

    public static function atualizarSenha(int $usuarioId, string $novaSenha): void
    {
        $pdo = conectarBanco();
        $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $pdo->prepare('UPDATE usuarios SET senha_hash = :senha WHERE id = :id')
            ->execute(['senha' => $hash, 'id' => $usuarioId]);
    }

    public static function salvarTokenReset(int $usuarioId, string $token, string $expiraEm): void
    {
        $pdo = conectarBanco();
        $pdo->prepare('DELETE FROM redefinicoes_senha WHERE usuario_id = :id')->execute(['id' => $usuarioId]);
        $pdo->prepare('INSERT INTO redefinicoes_senha (usuario_id, token, expira_em) VALUES (:id, :token, :expira)')
            ->execute(['id' => $usuarioId, 'token' => $token, 'expira' => $expiraEm]);
    }

    public static function buscarPorTokenReset(string $token): ?array
    {
        $pdo = conectarBanco();
        $stmt = $pdo->prepare('SELECT usuario_id, expira_em FROM redefinicoes_senha WHERE token = :token LIMIT 1');
        $stmt->execute(['token' => $token]);
        return $stmt->fetch() ?: null;
    }

    public static function removerTokenReset(string $token): void
    {
        $pdo = conectarBanco();
        $pdo->prepare('DELETE FROM redefinicoes_senha WHERE token = :token')->execute(['token' => $token]);
    }

    // ---------- Administração ----------
    public static function listarTodos(): array
    {
        $pdo = conectarBanco();
        return $pdo->query(
            "SELECT u.*,
                (SELECT COUNT(*) FROM jogos j WHERE j.criado_por = u.id) AS total_jogos,
                (SELECT COUNT(*) FROM avaliacoes a WHERE a.usuario_id = u.id) AS total_avaliacoes
             FROM usuarios u ORDER BY u.criado_em DESC"
        )->fetchAll();
    }

    public static function alterarTipo(int $id, string $novoTipo): void
    {
        $pdo = conectarBanco();
        $pdo->prepare('UPDATE usuarios SET tipo = :tipo WHERE id = :id')->execute(['tipo' => $novoTipo, 'id' => $id]);
    }

    public static function excluir(int $id): void
    {
        $pdo = conectarBanco();
        $pdo->prepare('DELETE FROM usuarios WHERE id = :id')->execute(['id' => $id]);
    }

    // ---------- Perfil público ----------
    public static function estatisticas(int $usuarioId): array
    {
        $pdo = conectarBanco();
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) AS total_avaliacoes, ROUND(AVG(nota), 1) AS nota_media
             FROM avaliacoes WHERE usuario_id = :id'
        );
        $stmt->execute(['id' => $usuarioId]);
        return $stmt->fetch() ?: ['total_avaliacoes' => 0, 'nota_media' => null];
    }
}
