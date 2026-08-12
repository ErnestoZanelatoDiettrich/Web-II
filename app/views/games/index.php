<?php
/**
 * Variáveis esperadas: $jogos, $generos, $plataformas, $filtros
 */
function corNota($nota): string
{
    if ($nota === null) return 'bg-secondary';
    if ($nota >= 75) return 'bg-success';
    if ($nota >= 50) return 'bg-warning text-dark';
    return 'bg-danger';
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Catálogo de Jogos</h1>
</div>

<form method="get" action="index.php" class="row g-2 mb-4 bg-light p-3 rounded border">
    <input type="hidden" name="page" value="jogos">
    <div class="col-md-4">
        <input type="text" name="busca" class="form-control" placeholder="Buscar por título..."
               value="<?= e($filtros['busca'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <select name="genero" class="form-select">
            <option value="">Todos os gêneros</option>
            <?php foreach ($generos as $g): ?>
                <option value="<?= e($g) ?>" <?= ($filtros['genero'] ?? '') === $g ? 'selected' : '' ?>><?= e($g) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <select name="plataforma" class="form-select">
            <option value="">Todas as plataformas</option>
            <?php foreach ($plataformas as $p): ?>
                <option value="<?= e($p) ?>" <?= ($filtros['plataforma'] ?? '') === $p ? 'selected' : '' ?>><?= e($p) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2 d-grid">
        <button class="btn btn-dark" type="submit">Filtrar</button>
    </div>
</form>

<?php if (empty($jogos)): ?>
    <p class="text-muted">Nenhum jogo encontrado.</p>
<?php endif; ?>

<div class="row row-cols-1 row-cols-md-3 g-4">
    <?php foreach ($jogos as $jogo): ?>
        <div class="col">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5 class="card-title"><?= e($jogo['titulo']) ?></h5>
                        <span class="badge <?= corNota($jogo['metascore']) ?> fs-6">
                            <?= $jogo['metascore'] !== null ? (int) $jogo['metascore'] : '—' ?>
                        </span>
                    </div>
                    <p class="text-muted mb-1"><?= e($jogo['genero']) ?> · <?= e($jogo['plataforma']) ?> · <?= e((string) $jogo['ano_lancamento']) ?></p>
                    <p class="small mb-2">
                        Usuários:
                        <span class="badge <?= corNota($jogo['nota_usuarios']) ?>">
                            <?= $jogo['nota_usuarios'] !== null ? $jogo['nota_usuarios'] : '—' ?>
                        </span>
                        (<?= (int) $jogo['total_avaliacoes'] ?> avaliações)
                    </p>
                    <a href="index.php?page=jogo&id=<?= (int) $jogo['id'] ?>" class="btn btn-outline-dark btn-sm">Ver detalhes</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
