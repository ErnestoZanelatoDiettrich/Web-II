<?php
require_once __DIR__ . '/../../config/database.php';

class Jogo
{
    private static function baseSelect(): string
    {
        return "SELECT j.*, g.nome AS genero, p.nome AS plataforma,
                    u.nome AS autor_nome,
                    ROUND(AVG(CASE WHEN a.tipo = 'critica' THEN a.nota END), 0) AS metascore,
                    ROUND(AVG(CASE WHEN a.tipo = 'usuario' THEN a.nota END), 1) AS nota_usuarios,
                    COUNT(DISTINCT CASE WHEN a.tipo = 'critica' THEN a.id END) AS total_criticas,
                    COUNT(DISTINCT CASE WHEN a.tipo = 'usuario' THEN a.id END) AS total_avaliacoes
                FROM jogos j
                JOIN generos g ON g.id = j.genero_id
                JOIN plataformas p ON p.id = j.plataforma_id
                JOIN usuarios u ON u.id = j.criado_por
                LEFT JOIN avaliacoes a ON a.jogo_id = j.id";
    }

    public static function listar(array $filtros = [], int $pagina = 1, int $porPagina = 9): array
    {
        $pdo = conectarBanco();
        $sql = self::baseSelect() . ' WHERE 1=1';
        $params = [];

        if (!empty($filtros['busca'])) {
            $sql .= ' AND j.titulo LIKE :busca';
            $params['busca'] = '%' . $filtros['busca'] . '%';
        }
        if (!empty($filtros['genero_id'])) {
            $sql .= ' AND j.genero_id = :genero_id';
            $params['genero_id'] = (int) $filtros['genero_id'];
        }
        if (!empty($filtros['plataforma_id'])) {
            $sql .= ' AND j.plataforma_id = :plataforma_id';
            $params['plataforma_id'] = (int) $filtros['plataforma_id'];
        }

        $sql .= ' GROUP BY j.id';

        $ordenacoes = [
            'nota_desc'  => 'metascore DESC, nota_usuarios DESC',
            'nota_asc'   => 'metascore ASC, nota_usuarios ASC',
            'ano_desc'   => 'j.ano_lancamento DESC',
            'ano_asc'    => 'j.ano_lancamento ASC',
            'titulo_asc' => 'j.titulo ASC',
        ];
        $ordem = $ordenacoes[$filtros['ordenar'] ?? ''] ?? 'j.titulo ASC';
        $sql .= " ORDER BY $ordem";

        $offset = max(0, ($pagina - 1) * $porPagina);
        $sql .= " LIMIT $porPagina OFFSET $offset";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function contarTotal(array $filtros = []): int
    {
        $pdo = conectarBanco();
        $sql = 'SELECT COUNT(*) FROM jogos j WHERE 1=1';
        $params = [];

        if (!empty($filtros['busca'])) {
            $sql .= ' AND j.titulo LIKE :busca';
            $params['busca'] = '%' . $filtros['busca'] . '%';
        }
        if (!empty($filtros['genero_id'])) {
            $sql .= ' AND j.genero_id = :genero_id';
            $params['genero_id'] = (int) $filtros['genero_id'];
        }
        if (!empty($filtros['plataforma_id'])) {
            $sql .= ' AND j.plataforma_id = :plataforma_id';
            $params['plataforma_id'] = (int) $filtros['plataforma_id'];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public static function buscarPorId(int $id): ?array
    {
        $pdo = conectarBanco();
        $sql = self::baseSelect() . ' WHERE j.id = :id GROUP BY j.id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function maisBemAvaliados(int $limite = 4): array
    {
        $pdo = conectarBanco();
        $sql = self::baseSelect() . ' GROUP BY j.id HAVING metascore IS NOT NULL ORDER BY metascore DESC LIMIT ' . $limite;
        return $pdo->query($sql)->fetchAll();
    }

    public static function lancamentosRecentes(int $limite = 4): array
    {
        $pdo = conectarBanco();
        $sql = self::baseSelect() . ' GROUP BY j.id ORDER BY j.ano_lancamento DESC, j.criado_em DESC LIMIT ' . $limite;
        return $pdo->query($sql)->fetchAll();
    }

    public static function porUsuario(int $usuarioId): array
    {
        $pdo = conectarBanco();
        $sql = self::baseSelect() . ' WHERE j.criado_por = :id GROUP BY j.id ORDER BY j.criado_em DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $usuarioId]);
        return $stmt->fetchAll();
    }

    public static function criar(array $dados): int
    {
        $pdo = conectarBanco();
        $stmt = $pdo->prepare(
            'INSERT INTO jogos (titulo, genero_id, plataforma_id, desenvolvedora, publicadora, ano_lancamento, descricao, capa_url, criado_por, criado_em)
             VALUES (:titulo, :genero_id, :plataforma_id, :desenvolvedora, :publicadora, :ano, :descricao, :capa, :criado_por, NOW())'
        );
        $stmt->execute([
            'titulo'         => $dados['titulo'],
            'genero_id'      => $dados['genero_id'],
            'plataforma_id'  => $dados['plataforma_id'],
            'desenvolvedora' => $dados['desenvolvedora'],
            'publicadora'    => $dados['publicadora'],
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
        $campos = 'titulo = :titulo, genero_id = :genero_id, plataforma_id = :plataforma_id,
                desenvolvedora = :desenvolvedora, publicadora = :publicadora, ano_lancamento = :ano,
                descricao = :descricao';
        $params = [
            'titulo'         => $dados['titulo'],
            'genero_id'      => $dados['genero_id'],
            'plataforma_id'  => $dados['plataforma_id'],
            'desenvolvedora' => $dados['desenvolvedora'],
            'publicadora'    => $dados['publicadora'],
            'ano'            => $dados['ano_lancamento'],
            'descricao'      => $dados['descricao'],
            'id'             => $id,
        ];

        if (!empty($dados['capa_url'])) {
            $campos .= ', capa_url = :capa';
            $params['capa'] = $dados['capa_url'];
        }

        $stmt = $pdo->prepare("UPDATE jogos SET $campos WHERE id = :id");
        $stmt->execute($params);
    }

    public static function excluir(int $id): void
    {
        $pdo = conectarBanco();
        $pdo->prepare('DELETE FROM jogos WHERE id = :id')->execute(['id' => $id]);
    }
}
