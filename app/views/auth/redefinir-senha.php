<div class="row justify-content-center">
    <div class="col-md-5">
        <h1 class="h4 mb-3">Redefinir senha</h1>

        <?php if (!empty($erros)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0"><?php foreach ($erros as $erro): ?><li><?= e($erro) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>

        <form method="post" action="index.php?page=redefinir-senha" class="needs-validation" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(gerarTokenCsrf()) ?>">
            <input type="hidden" name="token" value="<?= e($token) ?>">
            <div class="mb-3">
                <label class="form-label">Nova senha</label>
                <input type="password" name="senha" class="form-control" required minlength="6">
                <div class="invalid-feedback">Mínimo de 6 caracteres.</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirmar nova senha</label>
                <input type="password" name="confirmar_senha" class="form-control" required minlength="6">
                <div class="invalid-feedback">As senhas devem coincidir.</div>
            </div>
            <button type="submit" class="btn btn-dark w-100">Redefinir</button>
        </form>
    </div>
</div>
