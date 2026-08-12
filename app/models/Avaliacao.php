<?php
require_once __DIR__ . '/../../config/database.php';

class Avaliacao
{
    public static function listarPorJogo(int $jogoId): array
    {
        $pdo = conectarBanco();
        $stmt = $pdo->prepare(
            'SELECT a.*, u.nome AS autor_nome
             FROM avaliacoes a
             JOIN usuarios u ON u.id = a.usuario_id
             WHERE a.jogo_id = :jogo_id
             ORDER BY a.criado_em DESC'
        );
        $stmt->execute(['jogo_id' => $jogoId]);
        return $stmt->fetchAll();
    }

    public static function usuarioJaAvaliou(int $jogoId, int $usuarioId): bool
    {
        $pdo = conectarBanco();
        $stmt = $pdo->prepare('SELECT id FROM avaliacoes WHERE jogo_id = :jogo_id AND usuario_id = :usuario_id');
        $stmt->execute(['jogo_id' => $jogoId, 'usuario_id' => $usuarioId]);
        return (bool) $stmt->fetch();
    }

    public static function criar(int $jogoId, int $usuarioId, int $nota, string $comentario, string $tipo): int
    {
        $pdo = conectarBanco();
        $stmt = $pdo->prepare(
            'INSERT INTO avaliacoes (jogo_id, usuario_id, nota, comentario, tipo, criado_em)
             VALUES (:jogo_id, :usuario_id, :nota, :comentario, :tipo, NOW())'
        );
        $stmt->execute([
            'jogo_id'    => $jogoId,
            'usuario_id' => $usuarioId,
            'nota'       => $nota,
            'comentario' => $comentario,
            'tipo'       => $tipo,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function excluir(int $id, int $usuarioId): void
    {
        $pdo = conectarBanco();
        // Um usuário só pode excluir a própria avaliação
        $stmt = $pdo->prepare('DELETE FROM avaliacoes WHERE id = :id AND usuario_id = :usuario_id');
        $stmt->execute(['id' => $id, 'usuario_id' => $usuarioId]);
    }
}
