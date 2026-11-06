<?php

declare(strict_types=1);

namespace Mecanica\Validacao;

use DateTimeImmutable;

/**
 * Normalização e regras de texto usadas pelos validadores.
 */
final class Texto
{
    /** Remove espaços nas pontas e espaços repetidos no meio. */
    public static function limpar(string $valor): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', $valor));
    }

    /** "joão DA silva" vira "João Da Silva". */
    public static function nomeProprio(string $valor): string
    {
        return mb_convert_case(mb_strtolower(self::limpar($valor)), MB_CASE_TITLE);
    }

    public static function somenteDigitos(string $valor): string
    {
        return (string) preg_replace('/\D/', '', $valor);
    }

    /** Letras (com acento) e espaços, entre $minimo e $maximo caracteres. */
    public static function somenteLetras(string $valor, int $minimo, int $maximo): bool
    {
        return preg_match('/^[\p{L} ]{' . $minimo . ',' . $maximo . '}$/u', $valor) === 1;
    }

    public static function tamanhoEntre(string $valor, int $minimo, int $maximo): bool
    {
        $tamanho = mb_strlen($valor);
        return $tamanho >= $minimo && $tamanho <= $maximo;
    }

    /** Data no formato do campo HTML date (aaaa-mm-dd), que precisa existir no calendário. */
    public static function data(string $valor): ?DateTimeImmutable
    {
        $data = DateTimeImmutable::createFromFormat('!Y-m-d', $valor);
        return $data !== false && $data->format('Y-m-d') === $valor ? $data : null;
    }
}
