<?php /** Variáveis: $usuario, $erros */ ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <h1 class="h4 mb-3">Meu perfil</h1>

        <?php if (!empty($erros)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0"><?php foreach ($erros as $erro): ?><li><?= e($erro) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>

        <form method="post" action="index.php?page=perfil" enctype="multipart/form-data" class="needs-validation" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(gerarTokenCsrf()) ?>">

            <div class="text-center mb-3">
                <img id="preview-avatar" src="<?= $usuario['avatar_url'] ? e($usuario['avatar_url']) : 'img/avatar-padrao.svg' ?>" class="avatar-grande mb-2" alt="">
                <br>
                <input type="file" name="avatar" accept="image/png, image/jpeg, image/webp" class="form-control" data-preview="preview-avatar">
                <div class="form-text">JPG, PNG ou WEBP — máx. 2MB.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" required minlength="3" value="<?= e($usuario['nome']) ?>">
                <div class="invalid-feedback">Informe seu nome.</div>
            </div>
            <div class="mb-3">
                <label class="form-label">E-mail</label>
                <input type="email" class="form-control" value="<?= e($usuario['email']) ?>" disabled>
                <div class="form-text">O e-mail não pode ser alterado neste esboço.</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Biografia</label>
                <textarea name="bio" class="form-control" rows="3" maxlength="500"><?= e($usuario['bio'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <span class="badge bg-warning text-dark"><?= e($usuario['tipo']) ?></span>
            </div>

            <button type="submit" class="btn btn-warning w-100">Salvar alterações</button>
        </form>
    </div>
</div>
