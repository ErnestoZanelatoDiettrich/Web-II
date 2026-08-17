<?php
require_once __DIR__ . '/../models/Jogo.php';
require_once __DIR__ . '/../models/Taxonomia.php';
require_once __DIR__ . '/../helpers/upload.php';

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

    private static function prepararDados(array $dados): array
    {
        $dados['genero_id'] = Genero::buscarOuCriar($dados['genero']);
        $dados['plataforma_id'] = Plataforma::buscarOuCriar($dados['plataforma']);
        return $dados;
    }

    public static function criar(array $dados, int $usuarioId, array $arquivoCapa): array
    {
        $erros = self::validar($dados);

        $upload = tratarUploadImagem($arquivoCapa, UPLOAD_CAPAS_DIR);
        if (!$upload['sucesso']) {
            $erros[] = $upload['erro'];
        }

        if ($erros) {
            return ['sucesso' => false, 'erros' => $erros];
        }

        $dados = self::prepararDados($dados);
        $dados['criado_por'] = $usuarioId;
        if ($upload['arquivo']) {
            $dados['capa_url'] = 'uploads/capas/' . $upload['arquivo'];
        }

        $id = Jogo::criar($dados);
        return ['sucesso' => true, 'id' => $id];
    }

    public static function atualizar(int $id, array $dados, array $arquivoCapa): array
    {
        $erros = self::validar($dados);

        $upload = tratarUploadImagem($arquivoCapa, UPLOAD_CAPAS_DIR);
        if (!$upload['sucesso']) {
            $erros[] = $upload['erro'];
        }

        if ($erros) {
            return ['sucesso' => false, 'erros' => $erros];
        }

        $dados = self::prepararDados($dados);
        if ($upload['arquivo']) {
            $dados['capa_url'] = 'uploads/capas/' . $upload['arquivo'];
        } else {
            $dados['capa_url'] = null; // mantém a capa existente (model só atualiza se vier valor)
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
