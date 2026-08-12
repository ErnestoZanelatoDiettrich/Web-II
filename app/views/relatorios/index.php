<h1 class="h4 mb-3">Relatório de Jogos</h1>
<p class="text-muted">Exporte os dados do catálogo (com os filtros aplicados) em formato CSV, compatível com Excel.</p>

<form method="get" action="index.php" class="row g-2 mb-4 bg-light p-3 rounded border">
    <input type="hidden" name="page" value="relatorio">
    <div class="col-md-4">
        <select name="genero" class="form-select">
            <option value="">Todos os gêneros</option>
            <?php foreach ($generos as $g): ?>
                <option value="<?= e($g) ?>" <?= ($filtros['genero'] ?? '') === $g ? 'selected' : '' ?>><?= e($g) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <select name="plataforma" class="form-select">
            <option value="">Todas as plataformas</option>
            <?php foreach ($plataformas as $p): ?>
                <option value="<?= e($p) ?>" <?= ($filtros['plataforma'] ?? '') === $p ? 'selected' : '' ?>><?= e($p) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4 d-grid">
        <button class="btn btn-dark" type="submit" name="exportar" value="1">Exportar CSV</button>
    </div>
</form>

<table class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Título</th><th>Gênero</th><th>Plataforma</th><th>Ano</th>
            <th>Metascore</th><th>Nota usuários</th><th>Nº avaliações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($jogos as $jogo): ?>
            <tr>
                <td><?= e($jogo['titulo']) ?></td>
                <td><?= e($jogo['genero']) ?></td>
                <td><?= e($jogo['plataforma']) ?></td>
                <td><?= e((string) $jogo['ano_lancamento']) ?></td>
                <td><?= $jogo['metascore'] ?? '-' ?></td>
                <td><?= $jogo['nota_usuarios'] ?? '-' ?></td>
                <td><?= (int) $jogo['total_avaliacoes'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
