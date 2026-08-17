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

        $tipo = $tipoUsuario === 'usuario' ? 'usuario' : 'critica'; // crítico e admin contam como crítica
        Avaliacao::criar($jogoId, $usuarioId, $nota, $comentario, $tipo);

        return ['sucesso' => true];
    }

    public static function excluir(int $id, int $usuarioId, bool $ehAdmin = false): array
    {
        Avaliacao::excluir($id, $usuarioId, $ehAdmin);
        return ['sucesso' => true];
    }
}
