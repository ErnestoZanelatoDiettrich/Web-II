<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trustic — Notas confiáveis para jogos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top trustic-navbar">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php?page=inicio">
            <i class="bi bi-shield-check text-warning"></i> Trustic
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="index.php?page=inicio">Início</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=jogos">Catálogo</a></li>
                <?php if (usuarioLogado()): ?>
                    <li class="nav-item"><a class="nav-link" href="index.php?page=favoritos">Favoritos</a></li>
                <?php endif; ?>
                <?php if (ehCritico()): ?>
                    <li class="nav-item"><a class="nav-link" href="index.php?page=jogo-novo">Cadastrar jogo</a></li>
                <?php endif; ?>
                <li class="nav-item"><a class="nav-link" href="index.php?page=relatorio">Relatório</a></li>
                <?php if (ehAdmin()): ?>
                    <li class="nav-item"><a class="nav-link" href="index.php?page=admin">Admin</a></li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav align-items-lg-center">
                <?php if (usuarioLogado()): $u = usuarioAtual(); ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                            <img src="<?= $u['avatar_url'] ? e($u['avatar_url']) : 'img/avatar-padrao.svg' ?>" class="avatar-navbar" alt="">
                            <?= e($u['nome']) ?>
                            <span class="badge bg-warning text-dark"><?= e($u['tipo']) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="index.php?page=perfil">Meu perfil</a></li>
                            <li><a class="dropdown-item" href="index.php?page=perfil-publico&id=<?= (int) $u['id'] ?>">Ver perfil público</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="index.php?page=logout">Sair</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="index.php?page=login">Entrar</a></li>
                    <li class="nav-item"><a class="btn btn-warning btn-sm ms-lg-2" href="index.php?page=cadastro">Criar conta</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container my-4">
    <?php if (!empty($_SESSION['flash_sucesso'])): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= e($_SESSION['flash_sucesso']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_sucesso']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_erro'])): ?>
        <div class="alert alert-danger alert-dismissible fade show"><?= e($_SESSION['flash_erro']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_erro']); ?>
    <?php endif; ?>
