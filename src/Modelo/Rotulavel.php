<?php

declare(strict_types=1);

namespace Mecanica\Modelo;

/**
 * Enum com um texto amigável para mostrar na tela.
 */
interface Rotulavel
{
    public function rotulo(): string;
}
