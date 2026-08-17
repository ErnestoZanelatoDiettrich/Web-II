<?php /** Variáveis: $jogos */ ?>

<h1 class="h4 mb-3"><i class="bi bi-heart-fill text-danger"></i> Meus favoritos</h1>

<?php if (!$jogos): ?>
    <p class="text-muted">Você ainda não favoritou nenhum jogo. <a href="index.php?page=jogos">Explore o catálogo</a>.</p>
<?php endif; ?>

<div class="row row-cols-1 row-cols-md-3 g-4">
    <?php foreach ($jogos as $jogo): ?>
        <div class="col">
            <div class="card trustic-card h-100">
                <img src="<?= e($jogo['capa_url'] ?: 'img/sem-capa.svg') ?>" class="capa-jogo" alt="">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h6 class="card-title"><?= e($jogo['titulo']) ?></h6>
                        <span class="nota-badge <?= corNota($jogo['metascore']) ?>">
                            <?= $jogo['metascore'] !== null ? (int) $jogo['metascore'] : '—' ?>
                        </span>
                    </div>
                    <p class="text-muted small mb-2"><?= e($jogo['genero']) ?> · <?= e($jogo['plataforma']) ?></p>
                    <a href="index.php?page=jogo&id=<?= (int) $jogo['id'] ?>" class="btn btn-outline-light btn-sm">Ver detalhes</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
