<div class="row justify-content-center">
    <div class="col-md-6">
        <h1 class="h4 mb-3">Criar conta</h1>

        <?php if (!empty($erros)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0"><?php foreach ($erros as $erro): ?><li><?= e($erro) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>

        <form method="post" action="index.php?page=cadastro" class="needs-validation" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(gerarTokenCsrf()) ?>">
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" required minlength="3"
                       value="<?= e($_POST['nome'] ?? '') ?>">
                <div class="invalid-feedback">Informe seu nome (mín. 3 caracteres).</div>
            </div>
            <div class="mb-3">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" class="form-control" required
                       value="<?= e($_POST['email'] ?? '') ?>">
                <div class="invalid-feedback">Informe um e-mail válido.</div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Senha</label>
                    <input type="password" name="senha" class="form-control" required minlength="6">
                    <div class="invalid-feedback">Mínimo de 6 caracteres.</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Confirmar senha</label>
                    <input type="password" name="confirmar_senha" class="form-control" required minlength="6">
                    <div class="invalid-feedback">As senhas devem coincidir.</div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Tipo de conta</label>
                <select name="tipo" class="form-select">
                    <option value="usuario">Usuário comum</option>
                    <option value="critico">Crítico (pode cadastrar jogos e avaliar como crítica oficial)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-warning w-100">Cadastrar</button>
        </form>
    </div>
</div>
