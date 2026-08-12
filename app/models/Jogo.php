<?php
require_once __DIR__ . '/../../config/database.php';

class Jogo
{
    public static function listar(array $filtros = []): array
    {
        $pdo = conectarBanco();

        $sql = "SELECT j.*,
                    ROUND(AVG(CASE WHEN a.tipo = 'critica' THEN a.nota END), 0) AS metascore,
                    ROUND(AVG(CASE WHEN a.tipo = 'usuario' THEN a.nota END), 1) AS nota_usuarios,
                    COUNT(DISTINCT CASE WHEN a.tipo = 'critica' THEN a.id END) AS total_criticas,
                    COUNT(DISTINCT CASE WHEN a.tipo = 'usuario' THEN a.id END) AS total_avaliacoes
                FROM jogos j
                LEFT JOIN avaliacoes a ON a.jogo_id = j.id
                WHERE 1=1";
        $params = [];

        if (!empty($filtros['genero'])) {
            $sql .= ' AND j.genero = :genero';
            $params['genero'] = $filtros['genero'];
        }
        if (!empty($filtros['plataforma'])) {
            $sql .= ' AND j.plataforma = :plataforma';
            $params['plataforma'] = $filtros['plataforma'];
        }
        if (!empty($filtros['busca'])) {
            $sql .= ' AND j.titulo LIKE :busca';
            $params['busca'] = '%' . $filtros['busca'] . '%';
        }

        $sql .= ' GROUP BY j.id ORDER BY j.titulo ASC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function buscarPorId(int $id): ?array
    {
        $pdo = conectarBanco();
        $sql = "SELECT j.*,
                    ROUND(AVG(CASE WHEN a.tipo = 'critica' THEN a.nota END), 0) AS metascore,
                    ROUND(AVG(CASE WHEN a.tipo = 'usuario' THEN a.nota END), 1) AS nota_usuarios
                FROM jogos j
                LEFT JOIN avaliacoes a ON a.jogo_id = j.id
                WHERE j.id = :id
                GROUP BY j.id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $jogo = $stmt->fetch();
        return $jogo ?: null;
    }

    public static function criar(array $dados): int
    {
        $pdo = conectarBanco();
        $stmt = $pdo->prepare(
            'INSERT INTO jogos (titulo, genero, plataforma, desenvolvedora, ano_lancamento, descricao, capa_url, criado_por, criado_em)
             VALUES (:titulo, :genero, :plataforma, :desenvolvedora, :ano, :descricao, :capa, :criado_por, NOW())'
        );
        $stmt->execute([
            'titulo'         => $dados['titulo'],
            'genero'         => $dados['genero'],
            'plataforma'     => $dados['plataforma'],
            'desenvolvedora' => $dados['desenvolvedora'],
            'ano'            => $dados['ano_lancamento'],
            'descricao'      => $dados['descricao'],
            'capa'           => $dados['capa_url'] ?: null,
            'criado_por'     => $dados['criado_por'],
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function atualizar(int $id, array $dados): void
    {
        $pdo = conectarBanco();
        $stmt = $pdo->prepare(
            'UPDATE jogos SET titulo = :titulo, genero = :genero, plataforma = :plataforma,
                desenvolvedora = :desenvolvedora, ano_lancamento = :ano, descricao = :descricao, capa_url = :capa
             WHERE id = :id'
        );
        $stmt->execute([
            'titulo'         => $dados['titulo'],
            'genero'         => $dados['genero'],
            'plataforma'     => $dados['plataforma'],
            'desenvolvedora' => $dados['desenvolvedora'],
            'ano'            => $dados['ano_lancamento'],
            'descricao'      => $dados['descricao'],
            'capa'           => $dados['capa_url'] ?: null,
            'id'             => $id,
        ]);
    }

    public static function excluir(int $id): void
    {
        $pdo = conectarBanco();
        $stmt = $pdo->prepare('DELETE FROM jogos WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function generosDisponiveis(): array
    {
        $pdo = conectarBanco();
        return $pdo->query('SELECT DISTINCT genero FROM jogos ORDER BY genero')->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function plataformasDisponiveis(): array
    {
        $pdo = conectarBanco();
        return $pdo->query('SELECT DISTINCT plataforma FROM jogos ORDER BY plataforma')->fetchAll(PDO::FETCH_COLUMN);
    }
}
