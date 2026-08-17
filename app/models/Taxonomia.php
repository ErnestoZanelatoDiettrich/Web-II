<?php
require_once __DIR__ . '/../../config/database.php';

class Genero
{
    public static function listarTodos(): array
    {
        $pdo = conectarBanco();
        return $pdo->query('SELECT * FROM generos ORDER BY nome')->fetchAll();
    }

    public static function buscarOuCriar(string $nome): int
    {
        $pdo = conectarBanco();
        $nome = trim($nome);
        $stmt = $pdo->prepare('SELECT id FROM generos WHERE nome = :nome');
        $stmt->execute(['nome' => $nome]);
        $existente = $stmt->fetch();
        if ($existente) {
            return (int) $existente['id'];
        }
        $pdo->prepare('INSERT INTO generos (nome) VALUES (:nome)')->execute(['nome' => $nome]);
        return (int) $pdo->lastInsertId();
    }
}

class Plataforma
{
    public static function listarTodas(): array
    {
        $pdo = conectarBanco();
        return $pdo->query('SELECT * FROM plataformas ORDER BY nome')->fetchAll();
    }

    public static function buscarOuCriar(string $nome): int
    {
        $pdo = conectarBanco();
        $nome = trim($nome);
        $stmt = $pdo->prepare('SELECT id FROM plataformas WHERE nome = :nome');
        $stmt->execute(['nome' => $nome]);
        $existente = $stmt->fetch();
        if ($existente) {
            return (int) $existente['id'];
        }
        $pdo->prepare('INSERT INTO plataformas (nome) VALUES (:nome)')->execute(['nome' => $nome]);
        return (int) $pdo->lastInsertId();
    }
}
