<?php
/** Variáveis: $jogos, $generos, $plataformas, $filtros, $paginaAtual, $totalPaginas */
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Catálogo de Jogos</h1>
</div>

<form method="get" action="index.php" class="row g-2 mb-4 p-3 rounded border" style="background-color: var(--trustic-surface);">
    <input type="hidden" name="page" value="jogos">
    <div class="col-md-4">
        <input type="text" name="busca" class="form-control" placeholder="Buscar por título..."
               value="<?= e($filtros['busca'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <select name="genero_id" class="form-select">
            <option value="">Todos os gêneros</option>
            <?php foreach ($generos as $g): ?>
                <option value="<?= (int) $g['id'] ?>" <?= (int) ($filtros['genero_id'] ?? 0) === (int) $g['id'] ? 'selected' : '' ?>>
                    <?= e($g['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <select name="plataforma_id" class="form-select">
            <option value="">Todas as plataformas</option>
            <?php foreach ($plataformas as $p): ?>
                <option value="<?= (int) $p['id'] ?>" <?= (int) ($filtros['plataforma_id'] ?? 0) === (int) $p['id'] ? 'selected' : '' ?>>
                    <?= e($p['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2 d-grid">
        <button class="btn btn-warning" type="submit">Filtrar</button>
    </div>
    <div class="col-md-4 mt-2">
        <select name="ordenar" class="form-select" onchange="this.form.submit()">
            <option value="titulo_asc" <?= ($filtros['ordenar'] ?? '') === 'titulo_asc' ? 'selected' : '' ?>>Ordenar: Título (A-Z)</option>
            <option value="nota_desc" <?= ($filtros['ordenar'] ?? '') === 'nota_desc' ? 'selected' : '' ?>>Ordenar: Melhor nota</option>
            <option value="nota_asc" <?= ($filtros['ordenar'] ?? '') === 'nota_asc' ? 'selected' : '' ?>>Ordenar: Pior nota</option>
            <option value="ano_desc" <?= ($filtros['ordenar'] ?? '') === 'ano_desc' ? 'selected' : '' ?>>Ordenar: Mais recentes</option>
            <option value="ano_asc" <?= ($filtros['ordenar'] ?? '') === 'ano_asc' ? 'selected' : '' ?>>Ordenar: Mais antigos</option>
        </select>
    </div>
</form>

<?php if (empty($jogos)): ?>
    <p class="text-muted">Nenhum jogo encontrado com esses filtros.</p>
<?php endif; ?>

<div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
    <?php foreach ($jogos as $jogo): ?>
        <div class="col">
            <div class="card trustic-card h-100 shadow-sm">
                <img src="<?= e($jogo['capa_url'] ?: 'img/sem-capa.svg') ?>" class="capa-jogo" alt="Capa de <?= e($jogo['titulo']) ?>">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5 class="card-title"><?= e($jogo['titulo']) ?></h5>
                        <span class="nota-badge <?= corNota($jogo['metascore']) ?>">
                            <?= $jogo['metascore'] !== null ? (int) $jogo['metascore'] : '—' ?>
                        </span>
                    </div>
                    <p class="text-muted mb-1"><?= e($jogo['genero']) ?> · <?= e($jogo['plataforma']) ?> · <?= e((string) $jogo['ano_lancamento']) ?></p>
                    <p class="small mb-2">
                        Usuários:
                        <span class="nota-badge <?= corNota($jogo['nota_usuarios']) ?>">
                            <?= $jogo['nota_usuarios'] !== null ? $jogo['nota_usuarios'] : '—' ?>
                        </span>
                        (<?= (int) $jogo['total_avaliacoes'] ?> avaliações)
                    </p>
                    <a href="index.php?page=jogo&id=<?= (int) $jogo['id'] ?>" class="btn btn-outline-light btn-sm">Ver detalhes</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if ($totalPaginas > 1): ?>
    <nav aria-label="Paginação">
        <ul class="pagination justify-content-center">
            <?php for ($p = 1; $p <= $totalPaginas; $p++):
                $query = array_merge($_GET, ['pagina' => $p]);
            ?>
                <li class="page-item <?= $p === $paginaAtual ? 'active' : '' ?>">
                    <a class="page-link" href="index.php?<?= http_build_query($query) ?>"><?= $p ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>
