<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../helpers/upload.php';

class ProfileController
{
    public static function atualizar(int $usuarioId, array $dados, array $arquivoAvatar): array
    {
        $erros = [];
        $nome = trim($dados['nome'] ?? '');
        $bio = trim($dados['bio'] ?? '');

        if (mb_strlen($nome) < 3) {
            $erros[] = 'O nome deve ter pelo menos 3 caracteres.';
        }
        if (mb_strlen($bio) > 500) {
            $erros[] = 'A biografia deve ter no máximo 500 caracteres.';
        }

        $upload = tratarUploadImagem($arquivoAvatar, UPLOAD_AVATARES_DIR);
        if (!$upload['sucesso']) {
            $erros[] = $upload['erro'];
        }

        if ($erros) {
            return ['sucesso' => false, 'erros' => $erros];
        }

        $avatarArquivo = $upload['arquivo'] ? 'uploads/avatares/' . $upload['arquivo'] : null;
        Usuario::atualizarPerfil($usuarioId, $nome, $bio, $avatarArquivo);

        return ['sucesso' => true, 'avatar_url' => $avatarArquivo];
    }
}
