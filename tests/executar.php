<?php

declare(strict_types=1);

/*
 * Testes sem dependências: php tests/executar.php
 * Usa SQLite em memória (extensão pdo_sqlite), então não precisa de MySQL.
 */

require dirname(__DIR__) . '/src/bootstrap.php';

use Mecanica\Banco\Conexao;
use Mecanica\Modelo\Combustivel;
use Mecanica\Modelo\Sexo;
use Mecanica\Repositorio\ClienteRepositorio;
use Mecanica\Repositorio\ServicoRepositorio;
use Mecanica\Validacao\ClienteValidador;
use Mecanica\Validacao\ServicoValidador;
use Mecanica\Web\Html;
use Mecanica\Web\Listagem;

$falhas = 0;
$total = 0;

function verificar(string $descricao, bool $condicao): void
{
    global $falhas, $total;
    $total++;
    if (!$condicao) {
        $falhas++;
        echo "FALHOU: {$descricao}" . PHP_EOL;
    }
}

function novoBanco(): PDO
{
    Conexao::configurar(['dsn' => 'sqlite::memory:']);
    $pdo = Conexao::obter();
    $pdo->exec((string) file_get_contents(__DIR__ . '/schema-sqlite.sql'));
    return $pdo;
}

$clienteValido = [
    'nome' => '  joão   DA silva ',
    'sexo' => 'masculino',
    'dataNascimento' => '2000-01-31',
    'cep' => '91.160-020',
    'numeroCasa' => '56b',
    'telefone' => '(51) 99999-8888',
];

// Validação de cliente
$resultado = ClienteValidador::validar($clienteValido);
verificar('cliente válido passa', $resultado->valido());
verificar('nome é padronizado', $resultado->objeto?->nome === 'João Da Silva');
verificar('CEP é normalizado', $resultado->objeto?->cep === '91160-020');
verificar('número fica em maiúsculas', $resultado->objeto?->numeroCasa === '56B');
verificar('telefone fica só com dígitos', $resultado->objeto?->telefone === '51999998888');

$resultado = ClienteValidador::validar(['dataNascimento' => '2023-02-30'] + $clienteValido);
verificar('data inexistente é recusada', isset($resultado->erros['dataNascimento']));

$resultado = ClienteValidador::validar(['dataNascimento' => (new DateTimeImmutable('+1 day'))->format('Y-m-d')] + $clienteValido);
verificar('nascimento no futuro é recusado', isset($resultado->erros['dataNascimento']));

$resultado = ClienteValidador::validar(['sexo' => 'outro', 'nome' => 'A1', 'telefone' => '123'] + $clienteValido);
verificar('sexo, nome e telefone inválidos geram 3 erros', count($resultado->erros) === 3);

// Validação de serviço
$servicoValido = [
    'descricao' => 'Trocar óleo',
    'filial' => 'santaRosa',
    'nomeMecanico' => 'joão borracheiro',
    'carro' => 'vw gol 1.0',
    'cor' => 'prata',
    'placa' => 'bra2e19',
    'combustivel' => 'etanol',
    'urgencia' => 'comUrgencia',
];
$resultado = ServicoValidador::validar($servicoValido);
verificar('serviço válido passa', $resultado->valido());
verificar('placa Mercosul em maiúsculas', $resultado->objeto?->placa === 'BRA2E19');
verificar('carro aceita números', $resultado->objeto?->carro === 'Vw Gol 1.0');
verificar('placa antiga é aceita', ServicoValidador::validar(['placa' => 'ABC-1234'] + $servicoValido)->valido());
verificar('placa inválida é recusada', !ServicoValidador::validar(['placa' => 'AB-12'] + $servicoValido)->valido());
verificar('combustível desconhecido é recusado', !ServicoValidador::validar(['combustivel' => 'nitro'] + $servicoValido)->valido());

// Repositório de clientes
$clientes = new ClienteRepositorio(novoBanco());
$cliente = ClienteValidador::validar($clienteValido)->objeto;
$clientes->salvar($cliente);
verificar('inserir preenche o id', $cliente->id === 1);

$cliente->nome = 'João Atualizado';
$clientes->salvar($cliente);
$lido = $clientes->buscar(1);
verificar('atualizar grava o nome', $lido?->nome === 'João Atualizado');
verificar('sexo volta como enum', $lido?->sexo === Sexo::Masculino);

verificar('pesquisa por parte do nome', count($clientes->listar('nome', 'atual')) === 1);
verificar('pesquisa por código', count($clientes->listar('codigo', '1')) === 1);
verificar('filtro desconhecido lista todos', count($clientes->listar('senha', 'x')) === 1);
verificar(
    'tentativa de SQL injection não devolve registros',
    $clientes->listar('nome', "' OR '1'='1") === [] && $clientes->listar('codigo', '1 OR 1=1') !== []
        && count($clientes->listar('codigo', '0 OR 1=1')) === 0
);

$clientes->excluir(1);
verificar('excluir remove', $clientes->buscar(1) === null);

// Ordenação, paginação e contagem
foreach (['Bia', 'Caio', 'Ana'] as $nome) {
    $extra = ClienteValidador::validar(['nome' => $nome] + $clienteValido)->objeto;
    $clientes->salvar($extra);
}
verificar('contar devolve o total', $clientes->contar() === count($clientes->listar()));
verificar('contar respeita a pesquisa', $clientes->contar('nome', 'Bia') === 1);
verificar('ordem por nome crescente', $clientes->listar('', '', 'nome', 'asc')[0]->nome === 'Ana');
verificar('ordem por nome decrescente', $clientes->listar('', '', 'nome', 'desc')[0]->nome !== 'Ana');
verificar('limite e deslocamento paginam', count($clientes->listar('', '', 'nome', 'asc', 2, 0)) === 2);
verificar('ordem inválida cai no padrão (sem injeção)', count($clientes->listar('', '', 'nome; DROP TABLE cliente', 'asc')) > 0);

$lista = Listagem::ler(['ordem' => 'x', 'direcao' => 'y', 'pagina' => '-3'], ['id', 'nome'], 'nome', 'asc');
verificar('listagem ignora ordem e direção inválidas', $lista->ordem === 'nome' && $lista->direcao === 'asc');
verificar('listagem ignora página inválida', $lista->pagina === 1);
$lista = Listagem::ler(['pagina' => '99'], ['id'], 'id', 'asc')->comTotal(25);
verificar('página além do fim volta à última', $lista->pagina === 3 && $lista->totalPaginas() === 3);
verificar('link de ordenar inverte a direção', str_contains($lista->urlOrdenar('id'), 'direcao=desc'));

// Repositório de serviços
$servicos = new ServicoRepositorio(Conexao::obter());
$servico = ServicoValidador::validar($servicoValido)->objeto;
$servicos->salvar($servico);
verificar('serviço inserido e lido', $servicos->buscar($servico->id)?->combustivel === Combustivel::Etanol);
verificar('pesquisa de serviço por placa', count($servicos->listar('placa', 'BRA')) === 1);

// HTML
verificar('escape de HTML', Html::e('<script>"x"</script>') === '&lt;script&gt;&quot;x&quot;&lt;/script&gt;');
verificar('telefone celular formatado', Html::telefone('51999998888') === '(51) 99999-8888');
verificar('telefone fixo formatado', Html::telefone('5133334444') === '(51) 3333-4444');

echo PHP_EOL . ($total - $falhas) . " de {$total} verificações passaram." . PHP_EOL;
exit($falhas === 0 ? 0 : 1);
