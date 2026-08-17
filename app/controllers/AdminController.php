<?php
require_once __DIR__ . '/../models/Usuario.php';

class AdminController
{
    private static array $tiposValidos = ['usuario', 'critico', 'admin'];

    public static function alterarTipoUsuario(int $id, string $novoTipo, int $adminAtualId): array
    {
        if (!in_array($novoTipo, self::$tiposValidos, true)) {
            return ['sucesso' => false, 'erro' => 'Tipo de usuário inválido.'];
        }
        if ($id === $adminAtualId) {
            return ['sucesso' => false, 'erro' => 'Você não pode alterar seu próprio nível de acesso.'];
        }

        Usuario::alterarTipo($id, $novoTipo);
        return ['sucesso' => true];
    }

    public static function excluirUsuario(int $id, int $adminAtualId): array
    {
        if ($id === $adminAtualId) {
            return ['sucesso' => false, 'erro' => 'Você não pode excluir sua própria conta pelo painel.'];
        }

        Usuario::excluir($id);
        return ['sucesso' => true];
    }
}
