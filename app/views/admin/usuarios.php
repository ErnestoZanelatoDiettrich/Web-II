<?php /** Variáveis: $usuarios */ ?>

<h1 class="h4 mb-3"><i class="bi bi-gear text-warning"></i> Painel administrativo</h1>
<p class="text-muted">Gerencie os níveis de acesso dos usuários da plataforma.</p>

<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Nome</th><th>E-mail</th><th>Tipo</th><th>Jogos</th><th>Avaliações</th><th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= e($u['nome']) ?></td>
                    <td><?= e($u['email']) ?></td>
                    <td><span class="badge bg-warning text-dark"><?= e($u['tipo']) ?></span></td>
                    <td><?= (int) $u['total_jogos'] ?></td>
                    <td><?= (int) $u['total_avaliacoes'] ?></td>
                    <td class="d-flex gap-1 flex-wrap">
                        <?php if ($u['id'] != usuarioAtual()['id']): ?>
                            <form method="post" action="index.php?page=admin-usuario-tipo" class="d-flex gap-1">
                                <input type="hidden" name="csrf_token" value="<?= e(gerarTokenCsrf()) ?>">
                                <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                <select name="tipo" class="form-select form-select-sm">
                                    <option value="usuario" <?= $u['tipo'] === 'usuario' ? 'selected' : '' ?>>usuario</option>
                                    <option value="critico" <?= $u['tipo'] === 'critico' ? 'selected' : '' ?>>critico</option>
                                    <option value="admin" <?= $u['tipo'] === 'admin' ? 'selected' : '' ?>>admin</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-light">Salvar</button>
                            </form>
                            <form method="post" action="index.php?page=admin-usuario-excluir" onsubmit="return confirm('Excluir este usuário e todo o seu conteúdo?');">
                                <input type="hidden" name="csrf_token" value="<?= e(gerarTokenCsrf()) ?>">
                                <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                            </form>
                        <?php else: ?>
                            <span class="text-muted small">você</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
