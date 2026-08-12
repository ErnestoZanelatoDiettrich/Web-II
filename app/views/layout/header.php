<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CriticaJá - Notas e Críticas de Jogos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php?page=jogos">🎮 Critica<span class="text-warning">Já</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="index.php?page=jogos">Jogos</a></li>
                <?php if (usuarioLogado() && usuarioAtual()['tipo'] === 'critico'): ?>
                    <li class="nav-item"><a class="nav-link" href="index.php?page=jogo-novo">Cadastrar jogo</a></li>
                <?php endif; ?>
                <li class="nav-item"><a class="nav-link" href="index.php?page=relatorio">Relatório</a></li>
            </ul>
            <ul class="navbar-nav">
                <?php if (usuarioLogado()): ?>
                    <li class="nav-item d-flex align-items-center text-light me-3">
                        Olá, <?= e(usuarioAtual()['nome']) ?>
                        <span class="badge bg-warning text-dark ms-2"><?= e(usuarioAtual()['tipo']) ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm" href="index.php?page=logout">Sair</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="index.php?page=login">Entrar</a></li>
                    <li class="nav-item"><a class="btn btn-warning btn-sm ms-2" href="index.php?page=cadastro">Criar conta</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container my-4">
    <?php if (!empty($_SESSION['flash_sucesso'])): ?>
        <div class="alert alert-success"><?= e($_SESSION['flash_sucesso']) ?></div>
        <?php unset($_SESSION['flash_sucesso']); ?>
    <?php endif; ?>
