<?php

declare(strict_types=1);

namespace Mecanica\Web;

/**
 * Estado de uma lista na URL: pesquisa, filtro, ordenação e página.
 * Só aceita ordens da lista permitida; qualquer outro valor volta ao padrão.
 */
final class Listagem
{
    public const POR_PAGINA = 10;

    public int $total = 0;

    /**
     * @param array<string, string> $parametros pesquisa e filtro, para manter nos links
     */
    private function __construct(
        public readonly string $ordem,
        public readonly string $direcao,
        public readonly int $pagina,
        private readonly array $parametros,
    ) {
    }

    /**
     * @param array<string, mixed> $get
     * @param list<string> $ordenaveis chaves de ordenação permitidas
     */
    public static function ler(array $get, array $ordenaveis, string $ordemPadrao, string $direcaoPadrao): self
    {
        $ordem = (string) ($get['ordem'] ?? '');
        $ordem = in_array($ordem, $ordenaveis, true) ? $ordem : $ordemPadrao;
        $direcao = (string) ($get['direcao'] ?? '');
        $direcao = in_array($direcao, ['asc', 'desc'], true) ? $direcao : $direcaoPadrao;
        $pagina = filter_var($get['pagina'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        return new self($ordem, $direcao, $pagina === false ? 1 : $pagina, [
            'pesquisa' => (string) ($get['pesquisa'] ?? ''),
            'filtro' => (string) ($get['filtro'] ?? ''),
        ]);
    }

    /** Informa o total de registros e corrige a página se ela passou do fim. */
    public function comTotal(int $total): self
    {
        $nova = new self($this->ordem, $this->direcao, min($this->pagina, max(1, (int) ceil($total / self::POR_PAGINA))), $this->parametros);
        $nova->total = $total;

        return $nova;
    }

    public function totalPaginas(): int
    {
        return max(1, (int) ceil($this->total / self::POR_PAGINA));
    }

    public function deslocamento(): int
    {
        return ($this->pagina - 1) * self::POR_PAGINA;
    }

    /** Link para a mesma lista trocando alguns parâmetros. */
    public function url(array $mudancas = []): string
    {
        $dados = array_merge($this->parametros, ['ordem' => $this->ordem, 'direcao' => $this->direcao], $mudancas);
        $dados = array_filter($dados, static fn ($valor) => $valor !== '' && $valor !== null);

        return '?' . http_build_query($dados);
    }

    /** Link do cabeçalho de coluna: repete a coluna inverte a direção; volta para a página 1. */
    public function urlOrdenar(string $chave): string
    {
        $direcao = $this->ordem === $chave && $this->direcao === 'asc' ? 'desc' : 'asc';

        return $this->url(['ordem' => $chave, 'direcao' => $direcao, 'pagina' => 1]);
    }

    /** Célula <th> de cabeçalho clicável para ordenar por esta coluna. */
    public function cabecalho(string $chave, string $rotulo): string
    {
        return sprintf(
            '<th aria-sort="%s"><a href="%s">%s<i class="bi %s"></i></a></th>',
            $this->ariaOrdem($chave),
            Html::e($this->urlOrdenar($chave)),
            Html::e($rotulo),
            $this->iconeOrdem($chave),
        );
    }

    public function iconeOrdem(string $chave): string
    {
        if ($this->ordem !== $chave) {
            return 'bi-arrow-down-up';
        }

        return $this->direcao === 'asc' ? 'bi-sort-up' : 'bi-sort-down';
    }

    public function ariaOrdem(string $chave): string
    {
        if ($this->ordem !== $chave) {
            return 'none';
        }

        return $this->direcao === 'asc' ? 'ascending' : 'descending';
    }
}
