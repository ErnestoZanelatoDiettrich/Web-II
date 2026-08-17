<?php
/** Variáveis: $jogo (array|null), $erros, $acao, $generos, $plataformas */
$jogo = $jogo ?? [];
?>

<h1 class="h4 mb-3"><?= isset($jogo['id']) ? 'Editar jogo' : 'Cadastrar novo jogo' ?></h1>

<?php if (!empty($erros)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($erros as $erro): ?><li><?= e($erro) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= e($acao) ?>" enctype="multipart/form-data" class="needs-validation" novalidate>
    <input type="hidden" name="csrf_token" value="<?= e(gerarTokenCsrf()) ?>">

    <div class="row">
        <div class="col-md-8">
            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text" name="titulo" class="form-control" required minlength="2"
                       value="<?= e($jogo['titulo'] ?? '') ?>">
                <div class="invalid-feedback">Informe o título do jogo.</div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Gênero</label>
                    <input type="text" name="genero" class="form-control" required list="lista-generos"
                           value="<?= e($jogo['genero'] ?? '') ?>" placeholder="Ex: RPG, Ação">
                    <datalist id="lista-generos">
                        <?php foreach ($generos as $g): ?><option value="<?= e($g['nome']) ?>"><?php endforeach; ?>
                    </datalist>
                    <div class="invalid-feedback">Informe o gênero.</div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Plataforma</label>
                    <input type="text" name="plataforma" class="form-control" required list="lista-plataformas"
                           value="<?= e($jogo['plataforma'] ?? '') ?>" placeholder="Ex: PC, PS5">
                    <datalist id="lista-plataformas">
                        <?php foreach ($plataformas as $p): ?><option value="<?= e($p['nome']) ?>"><?php endforeach; ?>
                    </datalist>
                    <div class="invalid-feedback">Informe a plataforma.</div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Ano de lançamento</label>
                    <input type="number" name="ano_lancamento" class="form-control" required min="1970" max="<?= date('Y') + 1 ?>"
                           value="<?= e((string) ($jogo['ano_lancamento'] ?? '')) ?>">
                    <div class="invalid-feedback">Informe um ano válido.</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Desenvolvedora</label>
                    <input type="text" name="desenvolvedora" class="form-control"
                           value="<?= e($jogo['desenvolvedora'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Publicadora (opcional)</label>
                    <input type="text" name="publicadora" class="form-control"
                           value="<?= e($jogo['publicadora'] ?? '') ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Descrição</label>
                <textarea name="descricao" class="form-control" rows="4"><?= e($jogo['descricao'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="col-md-4">
            <label class="form-label">Capa do jogo</label>
            <img id="preview-capa" src="<?= e($jogo['capa_url'] ?? 'img/sem-capa.svg') ?>" class="img-fluid rounded border mb-2" style="max-height:220px; object-fit:cover; width:100%;" alt="">
            <input type="file" name="capa" accept="image/png, image/jpeg, image/webp" class="form-control" data-preview="preview-capa">
            <div class="form-text">JPG, PNG ou WEBP — máx. 2MB. Opcional ao editar (mantém a capa atual se não enviar outra).</div>
        </div>
    </div>

    <button type="submit" class="btn btn-warning">Salvar</button>
    <a href="index.php?page=jogos" class="btn btn-outline-light">Cancelar</a>
</form>
