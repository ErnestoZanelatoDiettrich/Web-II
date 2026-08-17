<div class="row justify-content-center">
    <div class="col-md-5">
        <h1 class="h4 mb-3">Entrar</h1>

        <?php if (!empty($erros)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0"><?php foreach ($erros as $erro): ?><li><?= e($erro) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>

        <form method="post" action="index.php?page=login" class="needs-validation" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(gerarTokenCsrf()) ?>">
            <div class="mb-3">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" class="form-control" required>
                <div class="invalid-feedback">Informe um e-mail válido.</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Senha</label>
                <input type="password" name="senha" class="form-control" required minlength="6">
                <div class="invalid-feedback">Informe sua senha.</div>
            </div>
            <button type="submit" class="btn btn-warning w-100">Entrar</button>
        </form>

        <div class="d-flex justify-content-between mt-3 small">
            <a href="index.php?page=recuperar-senha">Esqueci minha senha</a>
            <a href="index.php?page=cadastro">Criar conta</a>
        </div>
    </div>
</div>
