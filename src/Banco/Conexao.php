<?php

declare(strict_types=1);

namespace Mecanica\Banco;

use PDO;

/**
 * Conexão única com o banco, criada só quando for usada.
 */
final class Conexao
{
    /** @var array{dsn: string, usuario?: ?string, senha?: ?string} */
    private static array $config = ['dsn' => ''];
    private static ?PDO $pdo = null;

    /** @param array{dsn: string, usuario?: ?string, senha?: ?string} $config */
    public static function configurar(array $config): void
    {
        self::$config = $config;
        self::$pdo = null;
    }

    public static function obter(): PDO
    {
        return self::$pdo ??= new PDO(
            self::$config['dsn'],
            self::$config['usuario'] ?? null,
            self::$config['senha'] ?? null,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        );
    }
}
