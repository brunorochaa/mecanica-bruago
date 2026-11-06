<?php

declare(strict_types=1);

namespace Mecanica\Validacao;

use Mecanica\Modelo\Combustivel;
use Mecanica\Modelo\Filial;
use Mecanica\Modelo\Servico;
use Mecanica\Modelo\Urgencia;

final class ServicoValidador
{
    /**
     * @param array<string, mixed> $dados normalmente o $_POST
     * @return Resultado<Servico>
     */
    public static function validar(array $dados, ?int $id = null): Resultado
    {
        $valores = [
            'descricao' => Texto::limpar((string) ($dados['descricao'] ?? '')),
            'filial' => (string) ($dados['filial'] ?? ''),
            'nomeMecanico' => Texto::nomeProprio((string) ($dados['nomeMecanico'] ?? '')),
            'carro' => Texto::nomeProprio((string) ($dados['carro'] ?? '')),
            'cor' => Texto::nomeProprio((string) ($dados['cor'] ?? '')),
            'placa' => self::formatarPlaca((string) ($dados['placa'] ?? '')),
            'combustivel' => (string) ($dados['combustivel'] ?? ''),
            'urgencia' => (string) ($dados['urgencia'] ?? ''),
        ];
        $erros = [];

        if (!Texto::tamanhoEntre($valores['descricao'], 2, 200)) {
            $erros['descricao'] = 'Descreva o serviço (2 a 200 caracteres).';
        }

        $filial = Filial::tryFrom($valores['filial']);
        if ($filial === null) {
            $erros['filial'] = 'Selecione a filial.';
        }

        if (!Texto::somenteLetras($valores['nomeMecanico'], 2, 100)) {
            $erros['nomeMecanico'] = 'Informe o nome do mecânico com 2 a 100 letras.';
        }

        if (!Texto::tamanhoEntre($valores['carro'], 2, 150)) {
            $erros['carro'] = 'Informe a marca e o modelo do carro.';
        }

        if (!Texto::somenteLetras($valores['cor'], 2, 100)) {
            $erros['cor'] = 'Informe a cor do carro.';
        }

        if (preg_match('/^[A-Z]{3}-?\d[A-Z0-9]\d{2}$/', $valores['placa']) !== 1) {
            $erros['placa'] = 'Informe a placa no padrão antigo (ABC-1234) ou Mercosul (ABC1D23).';
        }

        $combustivel = Combustivel::tryFrom($valores['combustivel']);
        if ($combustivel === null) {
            $erros['combustivel'] = 'Selecione o combustível.';
        }

        $urgencia = Urgencia::tryFrom($valores['urgencia']);
        if ($urgencia === null) {
            $erros['urgencia'] = 'Selecione a urgência.';
        }

        $servico = $erros === [] ? new Servico(
            $id,
            $valores['descricao'],
            $filial,
            $valores['nomeMecanico'],
            $valores['carro'],
            $valores['cor'],
            $valores['placa'],
            $combustivel,
            $urgencia,
        ) : null;

        return new Resultado($valores, $erros, $servico);
    }

    private static function formatarPlaca(string $valor): string
    {
        return mb_strtoupper(str_replace(' ', '', trim($valor)));
    }
}
