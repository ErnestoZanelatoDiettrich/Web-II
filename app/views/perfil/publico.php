<?php /** Variáveis: $usuario, $estatisticas, $avaliacoes, $jogosCadastrados */ ?>

<div class="row mb-4">
    <div class="col-auto">
        <img src="<?= $usuario['avatar_url'] ? e($usuario['avatar_url']) : 'img/avatar-padrao.svg' ?>" class="avatar-grande" alt="">
    </div>
    <div class="col">
        <h1 class="h4 mb-1"><?= e($usuario['nome']) ?> <span class="badge bg-warning text-dark"><?= e($usuario['tipo']) ?></span></h1>
        <?php if (!empty($usuario['bio'])): ?>
            <p class="text-muted mb-2"><?= nl2br(e($usuario['bio'])) ?></p>
        <?php endif; ?>
        <p class="small text-muted mb-0">
            <?= (int) $estatisticas['total_avaliacoes'] ?> avaliações ·
            nota média dada: <?= $estatisticas['nota_media'] ?? '—' ?>
        </p>
    </div>
</div>

<?php if ($jogosCadastrados): ?>
    <h5 class="mb-3"><i class="bi bi-controller text-warning"></i> Jogos cadastrados</h5>
    <div class="row row-cols-1 row-cols-md-4 g-3 mb-4">
        <?php foreach ($jogosCadastrados as $jogo): ?>
            <div class="col">
                <a href="index.php?page=jogo&id=<?= (int) $jogo['id'] ?>" class="text-decoration-none">
                    <div class="card trustic-card h-100">
                        <img src="<?= e($jogo['capa_url'] ?: 'img/sem-capa.svg') ?>" class="capa-jogo" alt="">
                        <div class="card-body">
                            <h6 class="card-title mb-0"><?= e($jogo['titulo']) ?></h6>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h5 class="mb-3"><i class="bi bi-chat-square-text text-warning"></i> Avaliações recentes</h5>
<?php foreach ($avaliacoes as $a): ?>
    <div class="border-bottom py-2 d-flex align-items-start gap-3">
        <span class="nota-badge <?= corNota($a['nota']) ?>"><?= (int) $a['nota'] ?></span>
        <div>
            <a href="index.php?page=jogo&id=<?= (int) $a['jogo_id'] ?>"><strong><?= e($a['jogo_titulo']) ?></strong></a>
            <p class="mb-0 small"><?= nl2br(e($a['comentario'])) ?></p>
        </div>
    </div>
<?php endforeach; ?>
<?php if (!$avaliacoes): ?><p class="text-muted small">Nenhuma avaliação ainda.</p><?php endif; ?>
