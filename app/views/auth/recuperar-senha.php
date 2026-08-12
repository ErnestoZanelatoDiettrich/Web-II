<div class="row justify-content-center">
    <div class="col-md-5">
        <h1 class="h4 mb-3">Recuperar senha</h1>

        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-info"><?= $mensagem /* contém link de teste; ver observação no README */ ?></div>
        <?php endif; ?>

        <form method="post" action="index.php?page=recuperar-senha" class="needs-validation" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(gerarTokenCsrf()) ?>">
            <div class="mb-3">
                <label class="form-label">Informe seu e-mail cadastrado</label>
                <input type="email" name="email" class="form-control" required>
                <div class="invalid-feedback">Informe um e-mail válido.</div>
            </div>
            <button type="submit" class="btn btn-dark w-100">Enviar link de redefinição</button>
        </form>
    </div>
</div>
