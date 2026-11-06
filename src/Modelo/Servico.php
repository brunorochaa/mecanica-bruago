<?php

declare(strict_types=1);

namespace Mecanica\Modelo;

final class Servico
{
    public function __construct(
        public ?int $id,
        public string $descricao,
        public Filial $filial,
        public string $nomeMecanico,
        public string $carro,
        public string $cor,
        public string $placa,
        public Combustivel $combustivel,
        public Urgencia $urgencia,
    ) {
    }
}
