<?php
require_once __DIR__ . '/../models/Avaliacao.php';

class ReviewController
{
    public static function criar(int $jogoId, int $usuarioId, array $dados, string $tipoUsuario): array
    {
        $erros = [];
        $nota = (int) ($dados['nota'] ?? -1);
        $comentario = trim($dados['comentario'] ?? '');

        if ($nota < 0 || $nota > 100) {
            $erros[] = 'A nota deve ser um número entre 0 e 100.';
        }
        if (mb_strlen($comentario) < 5) {
            $erros[] = 'O comentário deve ter pelo menos 5 caracteres.';
        }
        if (Avaliacao::usuarioJaAvaliou($jogoId, $usuarioId)) {
            $erros[] = 'Você já avaliou este jogo.';
        }

        if ($erros) {
            return ['sucesso' => false, 'erros' => $erros];
        }

        // Críticos avaliam como "critica", usuários comuns como "usuario"
        $tipo = $tipoUsuario === 'critico' ? 'critica' : 'usuario';
        Avaliacao::criar($jogoId, $usuarioId, $nota, $comentario, $tipo);

        return ['sucesso' => true];
    }

    public static function excluir(int $id, int $usuarioId): array
    {
        Avaliacao::excluir($id, $usuarioId);
        return ['sucesso' => true];
    }
}
