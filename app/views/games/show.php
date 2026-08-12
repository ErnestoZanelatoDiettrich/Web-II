<?php
/**
 * Variáveis esperadas: $jogo, $avaliacoes, $jaAvaliou, $erros (opcional)
 */
function corNota2($nota): string
{
    if ($nota === null) return 'bg-secondary';
    if ($nota >= 75) return 'bg-success';
    if ($nota >= 50) return 'bg-warning text-dark';
    return 'bg-danger';
}
$criticas = array_filter($avaliacoes, fn($a) => $a['tipo'] === 'critica');
$usuarios = array_filter($avaliacoes, fn($a) => $a['tipo'] === 'usuario');
?>

<div class="row">
    <div class="col-md-8">
        <h1 class="h3"><?= e($jogo['titulo']) ?></h1>
        <p class="text-muted">
            <?= e($jogo['genero']) ?> · <?= e($jogo['plataforma']) ?> ·
            <?= e($jogo['desenvolvedora']) ?> · <?= e((string) $jogo['ano_lancamento']) ?>
        </p>
        <p><?= nl2br(e($jogo['descricao'])) ?></p>

        <?php if (usuarioLogado() && usuarioAtual()['tipo'] === 'critico' && usuarioAtual()['id'] == $jogo['criado_por']): ?>
            <a href="index.php?page=jogo-editar&id=<?= (int) $jogo['id'] ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
            <a href="index.php?page=jogo-excluir&id=<?= (int) $jogo['id'] ?>" class="btn btn-sm btn-outline-danger"
               onclick="return confirm('Excluir este jogo e todas as avaliações?');">Excluir</a>
        <?php endif; ?>
    </div>
    <div class="col-md-4 text-center">
        <div class="border rounded p-3">
            <div class="small text-uppercase text-muted">Metascore (críticos)</div>
            <span class="badge <?= corNota2($jogo['metascore']) ?> fs-1">
                <?= $jogo['metascore'] !== null ? (int) $jogo['metascore'] : '—' ?>
            </span>
        </div>
        <div class="border rounded p-3 mt-3">
            <div class="small text-uppercase text-muted">Nota dos usuários</div>
            <span class="badge <?= corNota2($jogo['nota_usuarios']) ?> fs-1">
                <?= $jogo['nota_usuarios'] !== null ? $jogo['nota_usuarios'] : '—' ?>
            </span>
        </div>
    </div>
</div>

<hr>

<?php if (!empty($erros)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($erros as $erro): ?><li><?= e($erro) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (usuarioLogado() && !$jaAvaliou): ?>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Deixe sua avaliação</h5>
            <form method="post" action="index.php?page=jogo&id=<?= (int) $jogo['id'] ?>" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e(gerarTokenCsrf()) ?>">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Nota (0-100)</label>
                        <input type="number" name="nota" min="0" max="100" class="form-control" required>
                        <div class="invalid-feedback">Informe uma nota entre 0 e 100.</div>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label">Comentário</label>
                        <textarea name="comentario" class="form-control" rows="2" minlength="5" required></textarea>
                        <div class="invalid-feedback">Escreva um comentário (mín. 5 caracteres).</div>
                    </div>
                </div>
                <button type="submit" class="btn btn-dark mt-3">Enviar avaliação</button>
            </form>
        </div>
    </div>
<?php elseif (!usuarioLogado()): ?>
    <p><a href="index.php?page=login">Entre</a> para avaliar este jogo.</p>
<?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <h5>Críticas (<?= count($criticas) ?>)</h5>
        <?php foreach ($criticas as $c): ?>
            <div class="border-bottom py-2">
                <span class="badge <?= corNota2($c['nota']) ?>"><?= (int) $c['nota'] ?></span>
                <strong><?= e($c['autor_nome']) ?></strong>
                <p class="mb-0 small"><?= nl2br(e($c['comentario'])) ?></p>
            </div>
        <?php endforeach; ?>
        <?php if (!$criticas): ?><p class="text-muted small">Nenhuma crítica ainda.</p><?php endif; ?>
    </div>
    <div class="col-md-6">
        <h5>Avaliações de usuários (<?= count($usuarios) ?>)</h5>
        <?php foreach ($usuarios as $u): ?>
            <div class="border-bottom py-2">
                <span class="badge <?= corNota2($u['nota']) ?>"><?= (int) $u['nota'] ?></span>
                <strong><?= e($u['autor_nome']) ?></strong>
                <?php if (usuarioLogado() && usuarioAtual()['id'] == $u['usuario_id']): ?>
                    <a href="index.php?page=avaliacao-excluir&id=<?= (int) $u['id'] ?>&jogo_id=<?= (int) $jogo['id'] ?>"
                       class="text-danger small float-end" onclick="return confirm('Excluir sua avaliação?');">excluir</a>
                <?php endif; ?>
                <p class="mb-0 small"><?= nl2br(e($u['comentario'])) ?></p>
            </div>
        <?php endforeach; ?>
        <?php if (!$usuarios): ?><p class="text-muted small">Nenhuma avaliação ainda.</p><?php endif; ?>
    </div>
</div>
