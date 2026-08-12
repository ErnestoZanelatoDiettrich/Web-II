<?php
/**
 * Conexão com o banco de dados usando PDO
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'criticaja');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

function conectarBanco(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $opcoes = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false, // evita SQL Injection, usa prepares reais
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $opcoes);
        } catch (PDOException $e) {
            // Nunca expor detalhes sensíveis do erro ao usuário final
            error_log('Erro de conexão com o banco: ' . $e->getMessage());
            die('Não foi possível conectar ao banco de dados. Tente novamente mais tarde.');
        }
    }

    return $pdo;
}
