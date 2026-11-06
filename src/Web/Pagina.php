<?php

declare(strict_types=1);

namespace Mecanica\Web;

/**
 * Ações comuns das páginas: método da requisição, redirecionamento,
 * mensagens de uma vez só (flash) e proteção CSRF dos formulários.
 */
final class Pagina
{
    public static function ehPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    public static function redirecionar(string $destino): never
    {
        header('Location: ' . $destino, true, 303);
        exit;
    }

    public static function naoEncontrado(string $mensagem): never
    {
        http_response_code(404);
        echo '<!DOCTYPE html><html lang="pt-BR"><meta charset="UTF-8"><title>Não encontrado</title><p>'
            . Html::e($mensagem) . '</p></html>';
        exit;
    }

    /** Lê um id inteiro positivo de $_GET ou $_POST. */
    public static function id(array $origem): ?int
    {
        $id = filter_var($origem['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        return $id === false ? null : $id;
    }

    public static function avisar(string $mensagem): void
    {
        $_SESSION['aviso'] = $mensagem;
    }

    public static function tirarAviso(): ?string
    {
        $aviso = $_SESSION['aviso'] ?? null;
        unset($_SESSION['aviso']);
        return $aviso;
    }

    public static function campoCsrf(): string
    {
        return '<input type="hidden" name="csrf" value="' . Html::e(self::tokenCsrf()) . '">';
    }

    /** Interrompe a requisição se o formulário não veio desta aplicação. */
    public static function exigirCsrf(): void
    {
        $enviado = (string) ($_POST['csrf'] ?? '');
        if (!hash_equals(self::tokenCsrf(), $enviado)) {
            http_response_code(400);
            exit('Formulário expirado. Volte, recarregue a página e tente de novo.');
        }
    }

    private static function tokenCsrf(): string
    {
        return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
    }
}
