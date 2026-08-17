<?php
require_once __DIR__ . '/../models/Usuario.php';

class AuthController
{
    public static function cadastrar(): array
    {
        $erros = [];
        $nome  = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';

        if (mb_strlen($nome) < 3) {
            $erros[] = 'O nome deve ter pelo menos 3 caracteres.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'Informe um e-mail válido.';
        }
        if (mb_strlen($senha) < 6) {
            $erros[] = 'A senha deve ter pelo menos 6 caracteres.';
        }
        if ($senha !== $confirmarSenha) {
            $erros[] = 'As senhas não coincidem.';
        }
        if (!$erros && Usuario::buscarPorEmail($email)) {
            $erros[] = 'Este e-mail já está cadastrado.';
        }

        if ($erros) {
            return ['sucesso' => false, 'erros' => $erros];
        }

        $tipo = ($_POST['tipo'] ?? 'usuario') === 'critico' ? 'critico' : 'usuario';
        $id = Usuario::criar($nome, $email, $senha, $tipo);

        return ['sucesso' => true, 'usuario_id' => $id];
    }

    public static function login(): array
    {
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        $usuario = Usuario::buscarPorEmail($email);

        if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
            return ['sucesso' => false, 'erros' => ['E-mail ou senha inválidos.']];
        }

        session_regenerate_id(true);

        $_SESSION['usuario_id']     = $usuario['id'];
        $_SESSION['usuario_nome']   = $usuario['nome'];
        $_SESSION['usuario_email']  = $usuario['email'];
        $_SESSION['usuario_tipo']   = $usuario['tipo'];
        $_SESSION['usuario_avatar'] = $usuario['avatar_url'];

        return ['sucesso' => true];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function solicitarRecuperacao(): array
    {
        $email = trim($_POST['email'] ?? '');
        $usuario = Usuario::buscarPorEmail($email);

        $mensagem = 'Se o e-mail existir em nossa base, um link de redefinição foi gerado.';

        if ($usuario) {
            $token = bin2hex(random_bytes(32));
            $expiraEm = date('Y-m-d H:i:s', time() + 3600);
            Usuario::salvarTokenReset((int) $usuario['id'], $token, $expiraEm);
            // Sem servidor de e-mail configurado: exibimos o link diretamente (uso acadêmico)
            $mensagem .= ' Link de teste: index.php?page=redefinir-senha&token=' . $token;
        }

        return ['sucesso' => true, 'mensagem' => $mensagem];
    }

    public static function redefinirSenha(): array
    {
        $token = $_POST['token'] ?? '';
        $novaSenha = $_POST['senha'] ?? '';
        $confirmar = $_POST['confirmar_senha'] ?? '';

        if (mb_strlen($novaSenha) < 6) {
            return ['sucesso' => false, 'erros' => ['A senha deve ter pelo menos 6 caracteres.']];
        }
        if ($novaSenha !== $confirmar) {
            return ['sucesso' => false, 'erros' => ['As senhas não coincidem.']];
        }

        $registro = Usuario::buscarPorTokenReset($token);
        if (!$registro || strtotime($registro['expira_em']) < time()) {
            return ['sucesso' => false, 'erros' => ['Link inválido ou expirado. Solicite uma nova recuperação.']];
        }

        Usuario::atualizarSenha((int) $registro['usuario_id'], $novaSenha);
        Usuario::removerTokenReset($token);

        return ['sucesso' => true];
    }
}
