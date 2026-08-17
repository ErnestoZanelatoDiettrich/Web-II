<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/GameController.php';
require_once __DIR__ . '/../app/controllers/ReviewController.php';
require_once __DIR__ . '/../app/controllers/ReportController.php';
require_once __DIR__ . '/../app/controllers/FavoriteController.php';
require_once __DIR__ . '/../app/controllers/ProfileController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/../app/models/Jogo.php';
require_once __DIR__ . '/../app/models/Avaliacao.php';
require_once __DIR__ . '/../app/models/Favorito.php';
require_once __DIR__ . '/../app/models/Usuario.php';
require_once __DIR__ . '/../app/models/Taxonomia.php';

$viewsPath = __DIR__ . '/../app/views';
$page = $_GET['page'] ?? 'inicio';

$ehPost = $_SERVER['REQUEST_METHOD'] === 'POST';
if ($ehPost && !validarTokenCsrf($_POST['csrf_token'] ?? null)) {
    http_response_code(400);
    die('Requisição inválida (token CSRF ausente ou expirado). Volte e tente novamente.');
}

switch ($page) {

    // ---------- HOME ----------
    case 'inicio':
        $maisBemAvaliados = Jogo::maisBemAvaliados(4);
        $lancamentos = Jogo::lancamentosRecentes(4);
        include "$viewsPath/layout/header.php";
        include "$viewsPath/home/index.php";
        include "$viewsPath/layout/footer.php";
        break;

    // ---------- AUTENTICAÇÃO ----------
    case 'cadastro':
        $erros = [];
        if ($ehPost) {
            $resultado = AuthController::cadastrar();
            if ($resultado['sucesso']) {
                flashSucesso('Conta criada com sucesso! Faça login.');
                redirecionar('index.php?page=login');
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
                redirecionar('index.php?page=inicio');
            }
            $erros = $resultado['erros'];
        }
        include "$viewsPath/layout/header.php";
        include "$viewsPath/auth/login.php";
        include "$viewsPath/layout/footer.php";
        break;

    case 'logout':
        AuthController::logout();
        redirecionar('index.php?page=login');

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
                flashSucesso('Senha redefinida com sucesso! Faça login.');
                redirecionar('index.php?page=login');
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
            'busca'         => $_GET['busca'] ?? '',
            'genero_id'     => $_GET['genero_id'] ?? '',
            'plataforma_id' => $_GET['plataforma_id'] ?? '',
            'ordenar'       => $_GET['ordenar'] ?? 'titulo_asc',
        ];
        $paginaAtual = max(1, (int) ($_GET['pagina'] ?? 1));
        $total = Jogo::contarTotal($filtros);
        $totalPaginas = (int) ceil($total / ITENS_POR_PAGINA);
        $jogos = Jogo::listar($filtros, $paginaAtual, ITENS_POR_PAGINA);
        $generos = Genero::listarTodos();
        $plataformas = Plataforma::listarTodas();
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
                redirecionar('index.php?page=jogo&id=' . $id);
            }
            $erros = $resultado['erros'];
        }
        $jogo = Jogo::buscarPorId($id);
        if (!$jogo) { http_response_code(404); die('Jogo não encontrado.'); }
        $avaliacoes = Avaliacao::listarPorJogo($id);
        $jaAvaliou = usuarioLogado() && Avaliacao::usuarioJaAvaliou($id, usuarioAtual()['id']);
        $ehFavorito = usuarioLogado() && Favorito::ehFavorito(usuarioAtual()['id'], $id);
        include "$viewsPath/layout/header.php";
        include "$viewsPath/games/show.php";
        include "$viewsPath/layout/footer.php";
        break;

    case 'jogo-novo':
        exigirCritico();
        $erros = [];
        if ($ehPost) {
            $resultado = GameController::criar($_POST, usuarioAtual()['id'], $_FILES['capa'] ?? []);
            if ($resultado['sucesso']) {
                redirecionar('index.php?page=jogo&id=' . $resultado['id']);
            }
            $erros = $resultado['erros'];
        }
        $acao = 'index.php?page=jogo-novo';
        $generos = Genero::listarTodos();
        $plataformas = Plataforma::listarTodas();
        include "$viewsPath/layout/header.php";
        include "$viewsPath/games/form.php";
        include "$viewsPath/layout/footer.php";
        break;

    case 'jogo-editar':
        exigirCritico();
        $id = (int) ($_GET['id'] ?? 0);
        $jogo = Jogo::buscarPorId($id);
        if (!$jogo || (!ehAdmin() && $jogo['criado_por'] != usuarioAtual()['id'])) {
            http_response_code(403); die('Você não tem permissão para editar este jogo.');
        }
        $erros = [];
        if ($ehPost) {
            $resultado = GameController::atualizar($id, $_POST, $_FILES['capa'] ?? []);
            if ($resultado['sucesso']) {
                redirecionar('index.php?page=jogo&id=' . $id);
            }
            $erros = $resultado['erros'];
            $jogo = array_merge($jogo, $_POST);
        }
        $acao = 'index.php?page=jogo-editar&id=' . $id;
        $generos = Genero::listarTodos();
        $plataformas = Plataforma::listarTodas();
        include "$viewsPath/layout/header.php";
        include "$viewsPath/games/form.php";
        include "$viewsPath/layout/footer.php";
        break;

    case 'jogo-excluir':
        exigirCritico();
        $id = (int) ($_GET['id'] ?? 0);
        $jogo = Jogo::buscarPorId($id);
        if ($jogo && (ehAdmin() || $jogo['criado_por'] == usuarioAtual()['id'])) {
            GameController::excluir($id);
            flashSucesso('Jogo excluído com sucesso.');
        }
        redirecionar('index.php?page=jogos');

    // ---------- AVALIAÇÕES ----------
    case 'avaliacao-excluir':
        exigirLogin();
        $id = (int) ($_GET['id'] ?? 0);
        $jogoId = (int) ($_GET['jogo_id'] ?? 0);
        ReviewController::excluir($id, usuarioAtual()['id'], ehAdmin());
        redirecionar('index.php?page=jogo&id=' . $jogoId);

    // ---------- FAVORITOS ----------
    case 'favorito-toggle':
        exigirLogin();
        $jogoId = (int) ($_POST['jogo_id'] ?? 0);
        FavoriteController::alternar(usuarioAtual()['id'], $jogoId);
        redirecionar('index.php?page=jogo&id=' . $jogoId);

    case 'favoritos':
        exigirLogin();
        $jogos = Favorito::listarPorUsuario(usuarioAtual()['id']);
        include "$viewsPath/layout/header.php";
        include "$viewsPath/perfil/favoritos.php";
        include "$viewsPath/layout/footer.php";
        break;

    // ---------- PERFIL ----------
    case 'perfil':
        exigirLogin();
        $erros = [];
        if ($ehPost) {
            $resultado = ProfileController::atualizar(usuarioAtual()['id'], $_POST, $_FILES['avatar'] ?? []);
            if ($resultado['sucesso']) {
                $_SESSION['usuario_nome'] = trim($_POST['nome']);
                if ($resultado['avatar_url']) {
                    $_SESSION['usuario_avatar'] = $resultado['avatar_url'];
                }
                flashSucesso('Perfil atualizado com sucesso.');
                redirecionar('index.php?page=perfil');
            }
            $erros = $resultado['erros'];
        }
        $usuario = Usuario::buscarPorId(usuarioAtual()['id']);
        include "$viewsPath/layout/header.php";
        include "$viewsPath/perfil/editar.php";
        include "$viewsPath/layout/footer.php";
        break;

    case 'perfil-publico':
        $id = (int) ($_GET['id'] ?? 0);
        $usuario = Usuario::buscarPorId($id);
        if (!$usuario) { http_response_code(404); die('Usuário não encontrado.'); }
        $estatisticas = Usuario::estatisticas($id);
        $avaliacoes = Avaliacao::listarPorUsuario($id);
        $jogosCadastrados = Jogo::porUsuario($id);
        include "$viewsPath/layout/header.php";
        include "$viewsPath/perfil/publico.php";
        include "$viewsPath/layout/footer.php";
        break;

    // ---------- ADMINISTRAÇÃO ----------
    case 'admin':
        exigirAdmin();
        $usuarios = Usuario::listarTodos();
        include "$viewsPath/layout/header.php";
        include "$viewsPath/admin/usuarios.php";
        include "$viewsPath/layout/footer.php";
        break;

    case 'admin-usuario-tipo':
        exigirAdmin();
        $id = (int) ($_POST['id'] ?? 0);
        $tipo = $_POST['tipo'] ?? '';
        $resultado = AdminController::alterarTipoUsuario($id, $tipo, usuarioAtual()['id']);
        $resultado['sucesso'] ? flashSucesso('Tipo de usuário atualizado.') : flashErro($resultado['erro']);
        redirecionar('index.php?page=admin');

    case 'admin-usuario-excluir':
        exigirAdmin();
        $id = (int) ($_POST['id'] ?? 0);
        $resultado = AdminController::excluirUsuario($id, usuarioAtual()['id']);
        $resultado['sucesso'] ? flashSucesso('Usuário excluído.') : flashErro($resultado['erro']);
        redirecionar('index.php?page=admin');

    // ---------- RELATÓRIO ----------
    case 'relatorio':
        $filtros = [
            'genero_id'     => $_GET['genero_id'] ?? '',
            'plataforma_id' => $_GET['plataforma_id'] ?? '',
        ];
        if (!empty($_GET['exportar'])) {
            ReportController::exportarCsv($filtros);
            exit;
        }
        $jogos = Jogo::listar($filtros, 1, 100000);
        $generos = Genero::listarTodos();
        $plataformas = Plataforma::listarTodas();
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
