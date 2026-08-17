<?php
require_once __DIR__ . '/../models/Jogo.php';

class ReportController
{
    public static function exportarCsv(array $filtros): void
    {
        $jogos = Jogo::listar($filtros, 1, 100000); // sem paginação no relatório

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="trustic_relatorio.csv"');

        $saida = fopen('php://output', 'w');
        fprintf($saida, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM para Excel

        fputcsv($saida, ['Título', 'Gênero', 'Plataforma', 'Ano', 'Metascore', 'Nota dos Usuários', 'Nº Críticas', 'Nº Avaliações', 'Cadastrado por']);

        foreach ($jogos as $jogo) {
            fputcsv($saida, [
                $jogo['titulo'], $jogo['genero'], $jogo['plataforma'], $jogo['ano_lancamento'],
                $jogo['metascore'] ?? '-', $jogo['nota_usuarios'] ?? '-',
                $jogo['total_criticas'], $jogo['total_avaliacoes'], $jogo['autor_nome'],
            ]);
        }

        fclose($saida);
        exit;
    }
}
