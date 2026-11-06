<?php

declare(strict_types=1);

namespace Mecanica\Repositorio;

use DateTimeImmutable;
use Mecanica\Modelo\Cliente;
use Mecanica\Modelo\Sexo;
use PDO;

final class ClienteRepositorio
{
    /** Campos que podem ser pesquisados: chave do filtro => [coluna, rótulo]. */
    public const CAMPOS_PESQUISA = [
        'nome' => ['nome', 'Nome'],
        'sexo' => ['sexo', 'Sexo'],
        'dataNascimento' => ['dataNascimento', 'Data de nascimento (aaaa-mm-dd)'],
        'cep' => ['cep', 'CEP'],
        'numeroCasa' => ['numeroCasa', 'Número da casa'],
        'telefone' => ['telefone', 'Telefone'],
    ];

    /** Colunas que podem ordenar a lista: chave da URL => coluna. */
    public const ORDENAVEIS = ['id' => 'idCliente', 'nome' => 'nome', 'nascimento' => 'dataNascimento'];

    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Lista os registros, com filtro opcional por um campo, ordenação e paginação.
     * Filtro "codigo" busca pelo código exato; os demais procuram o texto em qualquer parte.
     * Ordem e direção só aceitam valores das listas fixas (nunca vão direto para o SQL).
     *
     * @return list<Cliente>
     */
    public function listar(
        string $filtro = '',
        string $pesquisa = '',
        string $ordem = 'nome',
        string $direcao = 'asc',
        ?int $limite = null,
        int $deslocamento = 0,
    ): array {
        [$where, $parametros] = $this->condicao($filtro, $pesquisa);
        $coluna = self::ORDENAVEIS[$ordem] ?? 'nome';
        $sentido = strtolower($direcao) === 'desc' ? 'DESC' : 'ASC';
        $sql = "SELECT * FROM cliente {$where} ORDER BY {$coluna} {$sentido}, idCliente";
        if ($limite !== null) {
            $sql .= ' LIMIT ' . max(1, $limite) . ' OFFSET ' . max(0, $deslocamento);
        }
        $consulta = $this->pdo->prepare($sql);
        $consulta->execute($parametros);

        return array_map(self::montar(...), $consulta->fetchAll());
    }

    /** Quantos registros o mesmo filtro devolve (para paginar). */
    public function contar(string $filtro = '', string $pesquisa = ''): int
    {
        [$where, $parametros] = $this->condicao($filtro, $pesquisa);
        $consulta = $this->pdo->prepare("SELECT COUNT(*) FROM cliente {$where}");
        $consulta->execute($parametros);

        return (int) $consulta->fetchColumn();
    }

    /** @return array{0: string, 1: list<int|string>} cláusula WHERE e seus parâmetros */
    private function condicao(string $filtro, string $pesquisa): array
    {
        $pesquisa = trim($pesquisa);
        if ($pesquisa === '' || ($filtro !== 'codigo' && !isset(self::CAMPOS_PESQUISA[$filtro]))) {
            return ['', []];
        }
        if ($filtro === 'codigo') {
            return ['WHERE idCliente = ?', [(int) $pesquisa]];
        }

        return ['WHERE ' . self::CAMPOS_PESQUISA[$filtro][0] . ' LIKE ?', ['%' . $pesquisa . '%']];
    }

    public function buscar(int $id): ?Cliente
    {
        $consulta = $this->pdo->prepare('SELECT * FROM cliente WHERE idCliente = ?');
        $consulta->execute([$id]);
        $linha = $consulta->fetch();

        return $linha === false ? null : self::montar($linha);
    }

    public function salvar(Cliente $cliente): void
    {
        $parametros = [
            $cliente->nome,
            $cliente->sexo->value,
            $cliente->dataNascimento->format('Y-m-d'),
            $cliente->cep,
            $cliente->numeroCasa,
            $cliente->telefone,
        ];

        if ($cliente->id === null) {
            $this->pdo->prepare(
                'INSERT INTO cliente (nome, sexo, dataNascimento, cep, numeroCasa, telefone) VALUES (?, ?, ?, ?, ?, ?)'
            )->execute($parametros);
            $cliente->id = (int) $this->pdo->lastInsertId();
            return;
        }

        $this->pdo->prepare(
            'UPDATE cliente SET nome = ?, sexo = ?, dataNascimento = ?, cep = ?, numeroCasa = ?, telefone = ?
             WHERE idCliente = ?'
        )->execute([...$parametros, $cliente->id]);
    }

    public function excluir(int $id): void
    {
        $this->pdo->prepare('DELETE FROM cliente WHERE idCliente = ?')->execute([$id]);
    }

    /** @param array<string, mixed> $linha */
    private static function montar(array $linha): Cliente
    {
        return new Cliente(
            (int) $linha['idCliente'],
            $linha['nome'],
            Sexo::from($linha['sexo']),
            new DateTimeImmutable($linha['dataNascimento']),
            $linha['cep'],
            $linha['numeroCasa'],
            $linha['telefone'],
        );
    }
}
