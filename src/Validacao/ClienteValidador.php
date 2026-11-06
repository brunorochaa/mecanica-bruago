<?php

declare(strict_types=1);

namespace Mecanica\Validacao;

use DateTimeImmutable;
use Mecanica\Modelo\Cliente;
use Mecanica\Modelo\Sexo;

final class ClienteValidador
{
    /**
     * @param array<string, mixed> $dados normalmente o $_POST
     * @return Resultado<Cliente>
     */
    public static function validar(array $dados, ?int $id = null): Resultado
    {
        $valores = [
            'nome' => Texto::nomeProprio((string) ($dados['nome'] ?? '')),
            'sexo' => (string) ($dados['sexo'] ?? ''),
            'dataNascimento' => trim((string) ($dados['dataNascimento'] ?? '')),
            'cep' => self::formatarCep((string) ($dados['cep'] ?? '')),
            'numeroCasa' => mb_strtoupper(Texto::limpar((string) ($dados['numeroCasa'] ?? ''))),
            'telefone' => Texto::somenteDigitos((string) ($dados['telefone'] ?? '')),
        ];
        $erros = [];

        if (!Texto::somenteLetras($valores['nome'], 2, 100)) {
            $erros['nome'] = 'Informe o nome com 2 a 100 letras.';
        }

        $sexo = Sexo::tryFrom($valores['sexo']);
        if ($sexo === null) {
            $erros['sexo'] = 'Selecione o sexo.';
        }

        $dataNascimento = Texto::data($valores['dataNascimento']);
        if ($dataNascimento === null || $dataNascimento > new DateTimeImmutable('today')) {
            $erros['dataNascimento'] = 'Informe uma data de nascimento válida.';
        }

        if (preg_match('/^\d{5}-\d{3}$/', $valores['cep']) !== 1) {
            $erros['cep'] = 'Informe o CEP com 8 dígitos.';
        }

        if (preg_match('/^[A-Z0-9][A-Z0-9 \/-]{0,9}$/', $valores['numeroCasa']) !== 1) {
            $erros['numeroCasa'] = 'Informe o número (até 10 caracteres, ex.: 78 ou 56B).';
        }

        if (preg_match('/^\d{10,11}$/', $valores['telefone']) !== 1) {
            $erros['telefone'] = 'Informe o telefone com DDD (10 ou 11 dígitos).';
        }

        $cliente = $erros === [] ? new Cliente(
            $id,
            $valores['nome'],
            $sexo,
            $dataNascimento,
            $valores['cep'],
            $valores['numeroCasa'],
            $valores['telefone'],
        ) : null;

        return new Resultado($valores, $erros, $cliente);
    }

    /** Aceita 91160020, 91160-020 ou 91.160-020 e devolve 91160-020. */
    private static function formatarCep(string $valor): string
    {
        $digitos = Texto::somenteDigitos($valor);
        return strlen($digitos) === 8 ? substr($digitos, 0, 5) . '-' . substr($digitos, 5) : trim($valor);
    }
}
