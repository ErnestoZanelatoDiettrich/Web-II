<?php
/** Variáveis: $jogo, $avaliacoes, $jaAvaliou, $ehFavorito, $erros (opcional) */
$criticas = array_filter($avaliacoes, fn($a) => $a['tipo'] === 'critica');
$usuarios = array_filter($avaliacoes, fn($a) => $a['tipo'] === 'usuario');
$podeGerenciar = usuarioLogado() && (ehAdmin() || usuarioAtual()['id'] == $jogo['criado_por']);
?>

<div class="row">
    <div class="col-md-4">
        <img src="<?= e($jogo['capa_url'] ?: 'img/sem-capa.svg') ?>" class="img-fluid rounded border" style="width:100%; max-height:340px; object-fit:cover;" alt="Capa de <?= e($jogo['titulo']) ?>">
    </div>
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-start">
            <h1 class="h3"><?= e($jogo['titulo']) ?></h1>
            <?php if (usuarioLogado()): ?>
                <form method="post" action="index.php?page=favorito-toggle">
                    <input type="hidden" name="csrf_token" value="<?= e(gerarTokenCsrf()) ?>">
                    <input type="hidden" name="jogo_id" value="<?= (int) $jogo['id'] ?>">
                    <button type="submit" class="btn btn-outline-warning btn-favorito <?= $ehFavorito ? 'ativo' : '' ?>">
                        <i class="bi <?= $ehFavorito ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                        <?= $ehFavorito ? 'Favoritado' : 'Favoritar' ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>
        <p class="text-muted">
            <?= e($jogo['genero']) ?> · <?= e($jogo['plataforma']) ?> ·
            <?= e($jogo['desenvolvedora']) ?><?= $jogo['publicadora'] ? ' / ' . e($jogo['publicadora']) : '' ?> · <?= e((string) $jogo['ano_lancamento']) ?>
        </p>
        <p><?= nl2br(e($jogo['descricao'])) ?></p>
        <p class="small text-muted">Cadastrado por
            <a href="index.php?page=perfil-publico&id=<?= (int) $jogo['criado_por'] ?>"><?= e($jogo['autor_nome']) ?></a>
        </p>

        <div class="row text-center my-3">
            <div class="col-6">
                <div class="border rounded p-3">
                    <div class="small text-uppercase text-muted">Metascore</div>
                    <span class="nota-badge tamanho-grande <?= corNota($jogo['metascore']) ?>">
                        <?= $jogo['metascore'] !== null ? (int) $jogo['metascore'] : '—' ?>
                    </span>
                </div>
            </div>
            <div class="col-6">
                <div class="border rounded p-3">
                    <div class="small text-uppercase text-muted">Nota dos usuários</div>
                    <span class="nota-badge tamanho-grande <?= corNota($jogo['nota_usuarios']) ?>">
                        <?= $jogo['nota_usuarios'] !== null ? $jogo['nota_usuarios'] : '—' ?>
                    </span>
                </div>
            </div>
        </div>

        <?php if ($podeGerenciar): ?>
            <a href="index.php?page=jogo-editar&id=<?= (int) $jogo['id'] ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
            <a href="index.php?page=jogo-excluir&id=<?= (int) $jogo['id'] ?>" class="btn btn-sm btn-outline-danger"
               onclick="return confirm('Excluir este jogo e todas as avaliações?');">Excluir</a>
        <?php endif; ?>
    </div>
</div>

<hr>

<?php if (!empty($erros)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($erros as $erro): ?><li><?= e($erro) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<?php if (usuarioLogado() && !$jaAvaliou): ?>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Deixe sua avaliação <?= ehCritico() ? '<span class="badge bg-warning text-dark">crítica oficial</span>' : '' ?></h5>
            <form method="post" action="index.php?page=jogo&id=<?= (int) $jogo['id'] ?>" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e(gerarTokenCsrf()) ?>">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Nota (0-100)</label>
                        <input type="number" name="nota" min="0" max="100" class="form-control" required>
                        <div class="invalid-feedback">Informe uma nota entre 0 e 100.</div>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label">Comentário</label>
                        <textarea name="comentario" class="form-control" rows="2" minlength="5" required></textarea>
                        <div class="invalid-feedback">Escreva um comentário (mín. 5 caracteres).</div>
                    </div>
                </div>
                <button type="submit" class="btn btn-warning mt-3">Enviar avaliação</button>
            </form>
        </div>
    </div>
<?php elseif (!usuarioLogado()): ?>
    <p><a href="index.php?page=login">Entre</a> para avaliar este jogo.</p>
<?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <h5><i class="bi bi-award text-warning"></i> Críticas (<?= count($criticas) ?>)</h5>
        <?php foreach ($criticas as $c): ?>
            <div class="border-bottom py-2">
                <span class="nota-badge <?= corNota($c['nota']) ?>"><?= (int) $c['nota'] ?></span>
                <strong><a href="index.php?page=perfil-publico&id=<?= (int) $c['usuario_id'] ?>"><?= e($c['autor_nome']) ?></a></strong>
                <?php if (usuarioLogado() && (usuarioAtual()['id'] == $c['usuario_id'] || ehAdmin())): ?>
                    <a href="index.php?page=avaliacao-excluir&id=<?= (int) $c['id'] ?>&jogo_id=<?= (int) $jogo['id'] ?>"
                       class="text-danger small float-end" onclick="return confirm('Excluir esta avaliação?');">excluir</a>
                <?php endif; ?>
                <p class="mb-0 small"><?= nl2br(e($c['comentario'])) ?></p>
            </div>
        <?php endforeach; ?>
        <?php if (!$criticas): ?><p class="text-muted small">Nenhuma crítica ainda.</p><?php endif; ?>
    </div>
    <div class="col-md-6">
        <h5><i class="bi bi-people text-warning"></i> Avaliações de usuários (<?= count($usuarios) ?>)</h5>
        <?php foreach ($usuarios as $u2): ?>
            <div class="border-bottom py-2">
                <span class="nota-badge <?= corNota($u2['nota']) ?>"><?= (int) $u2['nota'] ?></span>
                <strong><a href="index.php?page=perfil-publico&id=<?= (int) $u2['usuario_id'] ?>"><?= e($u2['autor_nome']) ?></a></strong>
                <?php if (usuarioLogado() && (usuarioAtual()['id'] == $u2['usuario_id'] || ehAdmin())): ?>
                    <a href="index.php?page=avaliacao-excluir&id=<?= (int) $u2['id'] ?>&jogo_id=<?= (int) $jogo['id'] ?>"
                       class="text-danger small float-end" onclick="return confirm('Excluir esta avaliação?');">excluir</a>
                <?php endif; ?>
                <p class="mb-0 small"><?= nl2br(e($u2['comentario'])) ?></p>
            </div>
        <?php endforeach; ?>
        <?php if (!$usuarios): ?><p class="text-muted small">Nenhuma avaliação ainda.</p><?php endif; ?>
    </div>
</div>
