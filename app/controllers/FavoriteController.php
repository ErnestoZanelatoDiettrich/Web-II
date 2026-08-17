<?php
require_once __DIR__ . '/../models/Favorito.php';

class FavoriteController
{
    public static function alternar(int $usuarioId, int $jogoId): array
    {
        $adicionado = Favorito::alternar($usuarioId, $jogoId);
        return ['sucesso' => true, 'adicionado' => $adicionado];
    }
}
