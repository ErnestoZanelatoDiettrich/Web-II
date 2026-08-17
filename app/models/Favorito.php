<?php
require_once __DIR__ . '/../../config/database.php';

class Favorito
{
    public static function ehFavorito(int $usuarioId, int $jogoId): bool
    {
        $pdo = conectarBanco();
        $stmt = $pdo->prepare('SELECT 1 FROM favoritos WHERE usuario_id = :u AND jogo_id = :j');
        $stmt->execute(['u' => $usuarioId, 'j' => $jogoId]);
        return (bool) $stmt->fetch();
    }

    public static function alternar(int $usuarioId, int $jogoId): bool
    {
        $pdo = conectarBanco();
        if (self::ehFavorito($usuarioId, $jogoId)) {
            $pdo->prepare('DELETE FROM favoritos WHERE usuario_id = :u AND jogo_id = :j')
                ->execute(['u' => $usuarioId, 'j' => $jogoId]);
            return false; // removido
        }
        $pdo->prepare('INSERT INTO favoritos (usuario_id, jogo_id, criado_em) VALUES (:u, :j, NOW())')
            ->execute(['u' => $usuarioId, 'j' => $jogoId]);
        return true; // adicionado
    }

    public static function listarPorUsuario(int $usuarioId): array
    {
        $pdo = conectarBanco();
        $stmt = $pdo->prepare(
            "SELECT j.*, g.nome AS genero, p.nome AS plataforma,
                    ROUND(AVG(CASE WHEN a.tipo = 'critica' THEN a.nota END), 0) AS metascore,
                    ROUND(AVG(CASE WHEN a.tipo = 'usuario' THEN a.nota END), 1) AS nota_usuarios
             FROM favoritos f
             JOIN jogos j ON j.id = f.jogo_id
             JOIN generos g ON g.id = j.genero_id
             JOIN plataformas p ON p.id = j.plataforma_id
             LEFT JOIN avaliacoes a ON a.jogo_id = j.id
             WHERE f.usuario_id = :usuario_id
             GROUP BY j.id
             ORDER BY f.criado_em DESC"
        );
        $stmt->execute(['usuario_id' => $usuarioId]);
        return $stmt->fetchAll();
    }
}
