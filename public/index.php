<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/GameController.php';
require_once __DIR__ . '/../app/controllers/ReviewController.php';
require_once __DIR__ . '/../app/controllers/ReportController.php';
require_once __DIR__ . '/../app/models/Jogo.php';
require_once __DIR__ . '/../app/models/Avaliacao.php';

$viewsPath = __DIR__ . '/../app/views';
$page = $_GET['page'] ?? 'jogos';

// Todo POST precisa vir com um token CSRF válido
$ehPost = $_SERVER['REQUEST_METHOD'] === 'POST';
if ($ehPost && !validarTokenCsrf($_POST['csrf_token'] ?? null)) {
    http_response_code(400);
    die('Requisição inválida (token CSRF ausente ou expirado). Volte e tente novamente.');
}

switch ($page) {

    // ---------- AUTENTICAÇÃO ----------
    case 'cadastro':
        $erros = [];
        if ($ehPost) {
            $resultado = AuthController::cadastrar();
            if ($resultado['sucesso']) {
                $_SESSION['flash_sucesso'] = 'Conta criada com sucesso! Faça login.';
                header('Location: index.php?page=login');
                exit;
            }
            $erros = $resultado['erros'];
        }
        include "$viewsPath/layout/header.php";
        include "$viewsPath/auth/cadastro.php";
        include "$viewsPath/layout/footer.php";
        break;

    case 'login':
        $erros = [];
        if ($ehPost) {
            $resultado = AuthController::login();
            if ($resultado['sucesso']) {
                header('Location: index.php?page=jogos');
                exit;
            }
            $erros = $resultado['erros'];
        }
        include "$viewsPath/layout/header.php";
        include "$viewsPath/auth/login.php";
        include "$viewsPath/layout/footer.php";
        break;

    case 'logout':
        AuthController::logout();
        header('Location: index.php?page=login');
        exit;

    case 'recuperar-senha':
        $mensagem = null;
        if ($ehPost) {
            $resultado = AuthController::solicitarRecuperacao();
            $mensagem = $resultado['mensagem'];
        }
        include "$viewsPath/layout/header.php";
        include "$viewsPath/auth/recuperar-senha.php";
        include "$viewsPath/layout/footer.php";
        break;

    case 'redefinir-senha':
        $erros = [];
        $token = $_GET['token'] ?? ($_POST['token'] ?? '');
        if ($ehPost) {
            $resultado = AuthController::redefinirSenha();
            if ($resultado['sucesso']) {
                $_SESSION['flash_sucesso'] = 'Senha redefinida com sucesso! Faça login.';
                header('Location: index.php?page=login');
                exit;
            }
            $erros = $resultado['erros'];
        }
        include "$viewsPath/layout/header.php";
        include "$viewsPath/auth/redefinir-senha.php";
        include "$viewsPath/layout/footer.php";
        break;

    // ---------- JOGOS ----------
    case 'jogos':
        $filtros = [
            'busca'      => $_GET['busca'] ?? '',
            'genero'     => $_GET['genero'] ?? '',
            'plataforma' => $_GET['plataforma'] ?? '',
        ];
        $jogos = Jogo::listar($filtros);
        $generos = Jogo::generosDisponiveis();
        $plataformas = Jogo::plataformasDisponiveis();
        include "$viewsPath/layout/header.php";
        include "$viewsPath/games/index.php";
        include "$viewsPath/layout/footer.php";
        break;

    case 'jogo':
        $id = (int) ($_GET['id'] ?? 0);
        $erros = [];
        if ($ehPost) {
            exigirLogin();
            $resultado = ReviewController::criar($id, usuarioAtual()['id'], $_POST, usuarioAtual()['tipo']);
            if ($resultado['sucesso']) {
                header('Location: index.php?page=jogo&id=' . $id);
                exit;
            }
            $erros = $resultado['erros'];
        }
        $jogo = Jogo::buscarPorId($id);
        if (!$jogo) { http_response_code(404); die('Jogo não encontrado.'); }
        $avaliacoes = Avaliacao::listarPorJogo($id);
        $jaAvaliou = usuarioLogado() && Avaliacao::usuarioJaAvaliou($id, usuarioAtual()['id']);
        include "$viewsPath/layout/header.php";
        include "$viewsPath/games/show.php";
        include "$viewsPath/layout/footer.php";
        break;

    case 'jogo-novo':
        exigirCritico();
        $erros = [];
        if ($ehPost) {
            $resultado = GameController::criar($_POST, usuarioAtual()['id']);
            if ($resultado['sucesso']) {
                header('Location: index.php?page=jogo&id=' . $resultado['id']);
                exit;
            }
            $erros = $resultado['erros'];
        }
        $acao = 'index.php?page=jogo-novo';
        include "$viewsPath/layout/header.php";
        include "$viewsPath/games/form.php";
        include "$viewsPath/layout/footer.php";
        break;

    case 'jogo-editar':
        exigirCritico();
        $id = (int) ($_GET['id'] ?? 0);
        $jogo = Jogo::buscarPorId($id);
        if (!$jogo || $jogo['criado_por'] != usuarioAtual()['id']) {
            http_response_code(403); die('Você não tem permissão para editar este jogo.');
        }
        $erros = [];
        if ($ehPost) {
            $resultado = GameController::atualizar($id, $_POST);
            if ($resultado['sucesso']) {
                header('Location: index.php?page=jogo&id=' . $id);
                exit;
            }
            $erros = $resultado['erros'];
            $jogo = array_merge($jogo, $_POST);
        }
        $acao = 'index.php?page=jogo-editar&id=' . $id;
        include "$viewsPath/layout/header.php";
        include "$viewsPath/games/form.php";
        include "$viewsPath/layout/footer.php";
        break;

    case 'jogo-excluir':
        exigirCritico();
        $id = (int) ($_GET['id'] ?? 0);
        $jogo = Jogo::buscarPorId($id);
        if ($jogo && $jogo['criado_por'] == usuarioAtual()['id']) {
            GameController::excluir($id);
        }
        header('Location: index.php?page=jogos');
        exit;

    // ---------- AVALIAÇÕES ----------
    case 'avaliacao-excluir':
        exigirLogin();
        $id = (int) ($_GET['id'] ?? 0);
        $jogoId = (int) ($_GET['jogo_id'] ?? 0);
        ReviewController::excluir($id, usuarioAtual()['id']);
        header('Location: index.php?page=jogo&id=' . $jogoId);
        exit;

    // ---------- RELATÓRIO ----------
    case 'relatorio':
        $filtros = [
            'genero'     => $_GET['genero'] ?? '',
            'plataforma' => $_GET['plataforma'] ?? '',
        ];
        if (!empty($_GET['exportar'])) {
            ReportController::exportarCsv($filtros);
            exit;
        }
        $jogos = Jogo::listar($filtros);
        $generos = Jogo::generosDisponiveis();
        $plataformas = Jogo::plataformasDisponiveis();
        include "$viewsPath/layout/header.php";
        include "$viewsPath/relatorios/index.php";
        include "$viewsPath/layout/footer.php";
        break;

    default:
        http_response_code(404);
        include "$viewsPath/layout/header.php";
        echo '<p>Página não encontrada.</p>';
        include "$viewsPath/layout/footer.php";
}
