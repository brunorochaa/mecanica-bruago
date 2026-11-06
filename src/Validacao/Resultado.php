<?php

declare(strict_types=1);

namespace Mecanica\Validacao;

/**
 * Resultado de uma validação de formulário.
 *
 * @template T
 */
final class Resultado
{
    /**
     * @param array<string, string> $valores valores já normalizados, para preencher o formulário de novo
     * @param array<string, string> $erros mensagem de erro por campo
     * @param T|null $objeto objeto montado quando não há erros
     */
    public function __construct(
        public readonly array $valores,
        public readonly array $erros,
        public readonly mixed $objeto,
    ) {
    }

    public function valido(): bool
    {
        return $this->erros === [];
    }
}
