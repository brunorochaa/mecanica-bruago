<?php

declare(strict_types=1);

namespace Mecanica\Modelo;

enum Urgencia: string implements Rotulavel
{
    case SemUrgencia = 'semUrgencia';
    case ComUrgencia = 'comUrgencia';

    public function rotulo(): string
    {
        return match ($this) {
            self::SemUrgencia => 'Sem urgência',
            self::ComUrgencia => 'Com urgência',
        };
    }
}
