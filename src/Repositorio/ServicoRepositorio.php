<?php

declare(strict_types=1);

namespace Mecanica\Repositorio;

use Mecanica\Modelo\Combustivel;
use Mecanica\Modelo\Filial;
use Mecanica\Modelo\Servico;
use Mecanica\Modelo\Urgencia;
use PDO;

final class ServicoRepositorio
{
    /** Campos que podem ser pesquisados: chave do filtro => [coluna, rótulo]. */
    public const CAMPOS_PESQUISA = [
        'descricao' => ['descricaoServico', 'Descrição'],
        'filial' => ['filial', 'Filial'],
        'nomeMecanico' => ['nomeMecanico', 'Mecânico'],
        'carro' => ['carro', 'Carro'],
        'cor' => ['cor', 'Cor'],
        'placa' => ['placa', 'Placa'],
        'combustivel' => ['tipoCombustivel', 'Combustível'],
        'urgencia' => ['urgencia', 'Urgência'],
    ];

    /** Colunas que podem ordenar a lista: chave da URL => coluna. */
    public const ORDENAVEIS = ['id' => 'idServico', 'descricao' => 'descricaoServico', 'carro' => 'carro', 'filial' => 'filial', 'urgencia' => 'urgencia'];

    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Lista os registros, com filtro opcional por um campo, ordenação e paginação.
     * Filtro "codigo" busca pelo código exato; os demais procuram o texto em qualquer parte.
     * Ordem e direção só aceitam valores das listas fixas (nunca vão direto para o SQL).
     *
     * @return list<Servico>
     */
    public function listar(
        string $filtro = '',
        string $pesquisa = '',
        string $ordem = 'id',
        string $direcao = 'desc',
        ?int $limite = null,
        int $deslocamento = 0,
    ): array {
        [$where, $parametros] = $this->condicao($filtro, $pesquisa);
        $coluna = self::ORDENAVEIS[$ordem] ?? 'idServico';
        $sentido = strtolower($direcao) === 'desc' ? 'DESC' : 'ASC';
        $sql = "SELECT * FROM servico {$where} ORDER BY {$coluna} {$sentido}, idServico";
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
        $consulta = $this->pdo->prepare("SELECT COUNT(*) FROM servico {$where}");
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
            return ['WHERE idServico = ?', [(int) $pesquisa]];
        }

        return ['WHERE ' . self::CAMPOS_PESQUISA[$filtro][0] . ' LIKE ?', ['%' . $pesquisa . '%']];
    }

    public function buscar(int $id): ?Servico
    {
        $consulta = $this->pdo->prepare('SELECT * FROM servico WHERE idServico = ?');
        $consulta->execute([$id]);
        $linha = $consulta->fetch();

        return $linha === false ? null : self::montar($linha);
    }

    public function salvar(Servico $servico): void
    {
        $parametros = [
            $servico->descricao,
            $servico->filial->value,
            $servico->nomeMecanico,
            $servico->carro,
            $servico->cor,
            $servico->placa,
            $servico->combustivel->value,
            $servico->urgencia->value,
        ];

        if ($servico->id === null) {
            $this->pdo->prepare(
                'INSERT INTO servico (descricaoServico, filial, nomeMecanico, carro, cor, placa, tipoCombustivel, urgencia)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            )->execute($parametros);
            $servico->id = (int) $this->pdo->lastInsertId();
            return;
        }

        $this->pdo->prepare(
            'UPDATE servico SET descricaoServico = ?, filial = ?, nomeMecanico = ?, carro = ?, cor = ?, placa = ?,
             tipoCombustivel = ?, urgencia = ? WHERE idServico = ?'
        )->execute([...$parametros, $servico->id]);
    }

    public function excluir(int $id): void
    {
        $this->pdo->prepare('DELETE FROM servico WHERE idServico = ?')->execute([$id]);
    }

    /** @param array<string, mixed> $linha */
    private static function montar(array $linha): Servico
    {
        return new Servico(
            (int) $linha['idServico'],
            $linha['descricaoServico'],
            Filial::from($linha['filial']),
            $linha['nomeMecanico'],
            $linha['carro'],
            $linha['cor'],
            $linha['placa'],
            Combustivel::from($linha['tipoCombustivel']),
            Urgencia::from($linha['urgencia']),
        );
    }
}
