<?php

declare(strict_types=1);

namespace Mecanica\Modelo;

enum Filial: string implements Rotulavel
{
    case AssisBrasil = 'assisBrasil';
    case SantaRosa = 'santaRosa';

    public function rotulo(): string
    {
        return match ($this) {
            self::AssisBrasil => 'Filial Assis Brasil',
            self::SantaRosa => 'Filial Santa Rosa',
        };
    }
}
