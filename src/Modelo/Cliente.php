<?php

declare(strict_types=1);

namespace Mecanica\Modelo;

use DateTimeImmutable;

final class Cliente
{
    public function __construct(
        public ?int $id,
        public string $nome,
        public Sexo $sexo,
        public DateTimeImmutable $dataNascimento,
        public string $cep,
        public string $numeroCasa,
        public string $telefone,
    ) {
    }
}
