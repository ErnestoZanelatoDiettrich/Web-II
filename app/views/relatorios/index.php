<h1 class="h4 mb-3"><i class="bi bi-file-earmark-bar-graph text-warning"></i> Relatório de Jogos</h1>
<p class="text-muted">Exporte os dados do catálogo (com os filtros aplicados) em CSV (Excel) ou visualize uma versão para impressão em PDF.</p>

<form method="get" action="index.php" class="row g-2 mb-4 p-3 rounded border no-print" style="background-color: var(--trustic-surface);">
    <input type="hidden" name="page" value="relatorio">
    <div class="col-md-4">
        <select name="genero_id" class="form-select">
            <option value="">Todos os gêneros</option>
            <?php foreach ($generos as $g): ?>
                <option value="<?= (int) $g['id'] ?>" <?= (int) ($filtros['genero_id'] ?? 0) === (int) $g['id'] ? 'selected' : '' ?>><?= e($g['nome']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <select name="plataforma_id" class="form-select">
            <option value="">Todas as plataformas</option>
            <?php foreach ($plataformas as $p): ?>
                <option value="<?= (int) $p['id'] ?>" <?= (int) ($filtros['plataforma_id'] ?? 0) === (int) $p['id'] ? 'selected' : '' ?>><?= e($p['nome']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2 d-grid">
        <button class="btn btn-warning" type="submit" name="exportar" value="1"><i class="bi bi-download"></i> CSV</button>
    </div>
    <div class="col-md-2 d-grid">
        <button class="btn btn-outline-light" type="submit" formtarget="_blank"><i class="bi bi-printer"></i> Filtrar</button>
    </div>
</form>

<div class="text-end mb-2 no-print">
    <button class="btn btn-sm btn-outline-light" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir / Salvar como PDF</button>
</div>

<table class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Título</th><th>Gênero</th><th>Plataforma</th><th>Ano</th>
            <th>Metascore</th><th>Nota usuários</th><th>Nº avaliações</th><th>Cadastrado por</th>
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
                <td><?= e($jogo['autor_nome']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
