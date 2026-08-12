<?php
require_once __DIR__ . '/../models/Jogo.php';

class ReportController
{
    /**
     * Gera um relatório em CSV (compatível com Excel) dos jogos e suas notas,
     * aplicando os mesmos filtros usados na listagem.
     */
    public static function exportarCsv(array $filtros): void
    {
        $jogos = Jogo::listar($filtros);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="relatorio_jogos.csv"');

        $saida = fopen('php://output', 'w');
        // BOM para o Excel reconhecer UTF-8 corretamente
        fprintf($saida, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($saida, ['Título', 'Gênero', 'Plataforma', 'Desenvolvedora', 'Ano', 'Metascore', 'Nota dos Usuários', 'Nº Críticas', 'Nº Avaliações']);

        foreach ($jogos as $jogo) {
            fputcsv($saida, [
                $jogo['titulo'],
                $jogo['genero'],
                $jogo['plataforma'],
                $jogo['desenvolvedora'],
                $jogo['ano_lancamento'],
                $jogo['metascore'] ?? '-',
                $jogo['nota_usuarios'] ?? '-',
                $jogo['total_criticas'],
                $jogo['total_avaliacoes'],
            ]);
        }

        fclose($saida);
        exit;
    }
}
