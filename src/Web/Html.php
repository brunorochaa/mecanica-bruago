<?php

declare(strict_types=1);

namespace Mecanica\Web;

use DateTimeImmutable;

/**
 * Funções usadas nos templates. Todo texto vindo do usuário ou do banco passa por e().
 */
final class Html
{
    public static function e(?string $texto): string
    {
        return htmlspecialchars($texto ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** Atributo "selected"/"checked" quando os valores coincidem. */
    public static function marcado(string $atual, string $opcao, string $atributo = 'selected'): string
    {
        return $atual === $opcao ? $atributo : '';
    }

    /** Classe do Bootstrap para destacar campo com erro. */
    public static function classeErro(array $erros, string $campo): string
    {
        return isset($erros[$campo]) ? 'is-invalid' : '';
    }

    public static function mensagemErro(array $erros, string $campo): string
    {
        return isset($erros[$campo])
            ? '<div class="invalid-feedback">' . self::e($erros[$campo]) . '</div>'
            : '';
    }

    public static function data(DateTimeImmutable $data): string
    {
        return $data->format('d/m/Y');
    }

    /** 51985788704 vira (51) 98578-8704. */
    public static function telefone(string $digitos): string
    {
        return match (strlen($digitos)) {
            11 => sprintf('(%s) %s-%s', substr($digitos, 0, 2), substr($digitos, 2, 5), substr($digitos, 7)),
            10 => sprintf('(%s) %s-%s', substr($digitos, 0, 2), substr($digitos, 2, 4), substr($digitos, 6)),
            default => $digitos,
        };
    }
}
