<?php /** Variáveis: $maisBemAvaliados, $lancamentos */ ?>

<div class="trustic-hero text-center mb-5">
    <h1 class="display-5"><i class="bi bi-shield-check"></i> Trustic</h1>
    <p class="lead text-muted mb-4">Notas confiáveis de críticos e da comunidade para você escolher seu próximo jogo.</p>
    <form method="get" action="index.php" class="row g-2 justify-content-center">
        <input type="hidden" name="page" value="jogos">
        <div class="col-md-6">
            <input type="text" name="busca" class="form-control form-control-lg" placeholder="Buscar um jogo...">
        </div>
        <div class="col-auto">
            <button class="btn btn-warning btn-lg" type="submit"><i class="bi bi-search"></i> Buscar</button>
        </div>
    </form>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h4 mb-0"><i class="bi bi-trophy text-warning"></i> Mais bem avaliados</h2>
    <a href="index.php?page=jogos&ordenar=nota_desc" class="small">Ver todos &rarr;</a>
</div>
<div class="row row-cols-1 row-cols-md-4 g-3 mb-5">
    <?php foreach ($maisBemAvaliados as $jogo): ?>
        <div class="col">
            <a href="index.php?page=jogo&id=<?= (int) $jogo['id'] ?>" class="text-decoration-none">
                <div class="card trustic-card h-100">
                    <img src="<?= e($jogo['capa_url'] ?: 'img/sem-capa.svg') ?>" class="capa-jogo" alt="">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h6 class="card-title mb-0"><?= e($jogo['titulo']) ?></h6>
                            <span class="nota-badge <?= corNota($jogo['metascore']) ?>"><?= (int) $jogo['metascore'] ?></span>
                        </div>
                        <small class="text-muted"><?= e($jogo['genero']) ?> · <?= e((string) $jogo['ano_lancamento']) ?></small>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
    <?php if (!$maisBemAvaliados): ?><p class="text-muted">Ainda não há jogos avaliados por críticos.</p><?php endif; ?>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h4 mb-0"><i class="bi bi-stars text-warning"></i> Lançamentos recentes</h2>
    <a href="index.php?page=jogos&ordenar=ano_desc" class="small">Ver todos &rarr;</a>
</div>
<div class="row row-cols-1 row-cols-md-4 g-3">
    <?php foreach ($lancamentos as $jogo): ?>
        <div class="col">
            <a href="index.php?page=jogo&id=<?= (int) $jogo['id'] ?>" class="text-decoration-none">
                <div class="card trustic-card h-100">
                    <img src="<?= e($jogo['capa_url'] ?: 'img/sem-capa.svg') ?>" class="capa-jogo" alt="">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h6 class="card-title mb-0"><?= e($jogo['titulo']) ?></h6>
                            <span class="nota-badge <?= corNota($jogo['metascore']) ?>">
                                <?= $jogo['metascore'] !== null ? (int) $jogo['metascore'] : '—' ?>
                            </span>
                        </div>
                        <small class="text-muted"><?= e($jogo['plataforma']) ?> · <?= e((string) $jogo['ano_lancamento']) ?></small>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>
