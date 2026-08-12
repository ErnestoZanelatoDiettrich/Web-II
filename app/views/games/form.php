<?php
/**
 * Variáveis esperadas: $jogo (array ou null), $erros (array), $acao (URL do form)
 */
$jogo = $jogo ?? [];
?>

<h1 class="h4 mb-3"><?= isset($jogo['id']) ? 'Editar jogo' : 'Cadastrar novo jogo' ?></h1>

<?php if (!empty($erros)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($erros as $erro): ?><li><?= e($erro) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= e($acao) ?>" class="needs-validation" novalidate>
    <input type="hidden" name="csrf_token" value="<?= e(gerarTokenCsrf()) ?>">

    <div class="mb-3">
        <label class="form-label">Título</label>
        <input type="text" name="titulo" class="form-control" required minlength="2"
               value="<?= e($jogo['titulo'] ?? '') ?>">
        <div class="invalid-feedback">Informe o título do jogo.</div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Gênero</label>
            <input type="text" name="genero" class="form-control" required
                   value="<?= e($jogo['genero'] ?? '') ?>" placeholder="Ex: RPG, Ação">
            <div class="invalid-feedback">Informe o gênero.</div>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Plataforma</label>
            <input type="text" name="plataforma" class="form-control" required
                   value="<?= e($jogo['plataforma'] ?? '') ?>" placeholder="Ex: PC, PS5">
            <div class="invalid-feedback">Informe a plataforma.</div>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Ano de lançamento</label>
            <input type="number" name="ano_lancamento" class="form-control" required min="1970" max="<?= date('Y') + 1 ?>"
                   value="<?= e((string) ($jogo['ano_lancamento'] ?? '')) ?>">
            <div class="invalid-feedback">Informe um ano válido.</div>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Desenvolvedora</label>
        <input type="text" name="desenvolvedora" class="form-control"
               value="<?= e($jogo['desenvolvedora'] ?? '') ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">URL da capa (opcional)</label>
        <input type="url" name="capa_url" class="form-control"
               value="<?= e($jogo['capa_url'] ?? '') ?>" placeholder="https://...">
    </div>

    <div class="mb-3">
        <label class="form-label">Descrição</label>
        <textarea name="descricao" class="form-control" rows="4"><?= e($jogo['descricao'] ?? '') ?></textarea>
    </div>

    <button type="submit" class="btn btn-dark">Salvar</button>
    <a href="index.php?page=jogos" class="btn btn-outline-secondary">Cancelar</a>
</form>
