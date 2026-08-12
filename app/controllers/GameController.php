<?php
require_once __DIR__ . '/../models/Jogo.php';

class GameController
{
    private static function validar(array $dados): array
    {
        $erros = [];

        if (mb_strlen(trim($dados['titulo'] ?? '')) < 2) {
            $erros[] = 'O título deve ter pelo menos 2 caracteres.';
        }
        if (empty($dados['genero'])) {
            $erros[] = 'Informe o gênero.';
        }
        if (empty($dados['plataforma'])) {
            $erros[] = 'Informe a plataforma.';
        }
        $ano = (int) ($dados['ano_lancamento'] ?? 0);
        if ($ano < 1970 || $ano > ((int) date('Y') + 1)) {
            $erros[] = 'Informe um ano de lançamento válido.';
        }

        return $erros;
    }

    public static function criar(array $dados, int $usuarioId): array
    {
        $erros = self::validar($dados);
        if ($erros) {
            return ['sucesso' => false, 'erros' => $erros];
        }

        $dados['criado_por'] = $usuarioId;
        $id = Jogo::criar($dados);
        return ['sucesso' => true, 'id' => $id];
    }

    public static function atualizar(int $id, array $dados): array
    {
        $erros = self::validar($dados);
        if ($erros) {
            return ['sucesso' => false, 'erros' => $erros];
        }

        Jogo::atualizar($id, $dados);
        return ['sucesso' => true];
    }

    public static function excluir(int $id): array
    {
        Jogo::excluir($id);
        return ['sucesso' => true];
    }
}
