<?php

declare(strict_types=1);

namespace Mecanica\Modelo;

enum Combustivel: string implements Rotulavel
{
    case Comum = 'comum';
    case Diesel = 'diesel';
    case Etanol = 'etanol';
    case Gas = 'gas';
    case Hibrido = 'hibrido';

    public function rotulo(): string
    {
        return match ($this) {
            self::Comum => 'Gasolina comum',
            self::Diesel => 'Diesel',
            self::Etanol => 'Etanol',
            self::Gas => 'Gás',
            self::Hibrido => 'Híbrido',
        };
    }
}
