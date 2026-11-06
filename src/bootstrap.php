<?php

declare(strict_types=1);

/*
 * Carregado no topo de cada página: autoload das classes, configuração,
 * sessão e tratamento de erros.
 */

spl_autoload_register(static function (string $classe): void {
    $prefixo = 'Mecanica\\';
    if (!str_starts_with($classe, $prefixo)) {
        return;
    }
    $caminho = __DIR__ . '/' . str_replace('\\', '/', substr($classe, strlen($prefixo))) . '.php';
    if (is_file($caminho)) {
        require $caminho;
    }
});

use Mecanica\Banco\Conexao;

$arquivoConfig = dirname(__DIR__) . '/config/config.php';
if (!is_file($arquivoConfig)) {
    $arquivoConfig = dirname(__DIR__) . '/config/config.example.php';
}
Conexao::configurar(require $arquivoConfig);

if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}

set_exception_handler(static function (Throwable $erro): void {
    error_log((string) $erro);
    if (PHP_SAPI === 'cli') {
        fwrite(STDERR, $erro->getMessage() . PHP_EOL);
        exit(1);
    }
    http_response_code(500);
    echo '<!DOCTYPE html><html lang="pt-BR"><meta charset="UTF-8"><title>Erro</title>'
        . '<p>Não foi possível concluir a operação. Verifique a conexão com o banco de dados.</p></html>';
});
