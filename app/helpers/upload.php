<?php
/**
 * Upload seguro de imagens: valida tipo/tamanho real do arquivo (não confia na extensão)
 * e gera um nome de arquivo aleatório para evitar colisões e path traversal.
 */
function tratarUploadImagem(array $arquivo, string $diretorioDestino): array
{
    if (!isset($arquivo['error']) || $arquivo['error'] === UPLOAD_ERR_NO_FILE) {
        return ['sucesso' => true, 'arquivo' => null]; // upload opcional, nada enviado
    }

    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        return ['sucesso' => false, 'erro' => 'Falha ao enviar o arquivo. Tente novamente.'];
    }

    if ($arquivo['size'] > UPLOAD_MAX_BYTES) {
        return ['sucesso' => false, 'erro' => 'A imagem deve ter no máximo 2MB.'];
    }

    $tiposPermitidos = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    // Verifica o tipo real do arquivo (não confia apenas na extensão enviada pelo navegador)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeReal = finfo_file($finfo, $arquivo['tmp_name']);
    finfo_close($finfo);

    if (!isset($tiposPermitidos[$mimeReal])) {
        return ['sucesso' => false, 'erro' => 'Formato inválido. Envie uma imagem JPG, PNG ou WEBP.'];
    }

    if (!is_dir($diretorioDestino)) {
        mkdir($diretorioDestino, 0755, true);
    }

    $nomeArquivo = bin2hex(random_bytes(16)) . '.' . $tiposPermitidos[$mimeReal];
    $caminhoCompleto = rtrim($diretorioDestino, '/') . '/' . $nomeArquivo;

    if (!move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
        return ['sucesso' => false, 'erro' => 'Não foi possível salvar a imagem no servidor.'];
    }

    return ['sucesso' => true, 'arquivo' => $nomeArquivo];
}
