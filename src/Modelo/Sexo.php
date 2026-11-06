<?php

declare(strict_types=1);

namespace Mecanica\Modelo;

enum Sexo: string implements Rotulavel
{
    case Masculino = 'masculino';
    case Feminino = 'feminino';

    public function rotulo(): string
    {
        return match ($this) {
            self::Masculino => 'Masculino',
            self::Feminino => 'Feminino',
        };
    }
}
