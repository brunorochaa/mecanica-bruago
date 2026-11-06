<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/src/bootstrap.php';

use Mecanica\Banco\Conexao;
use Mecanica\Repositorio\ClienteRepositorio;
use Mecanica\Web\Html;
use Mecanica\Web\Listagem;
use Mecanica\Web\Pagina;

$filtro = (string) ($_GET['filtro'] ?? '');
$pesquisa = (string) ($_GET['pesquisa'] ?? '');
$repositorio = new ClienteRepositorio(Conexao::obter());
$listagem = Listagem::ler($_GET, array_keys(ClienteRepositorio::ORDENAVEIS), 'nome', 'asc')
    ->comTotal($repositorio->contar($filtro, $pesquisa));
$clientes = $repositorio->listar(
    $filtro,
    $pesquisa,
    $listagem->ordem,
    $listagem->direcao,
    Listagem::POR_PAGINA,
    $listagem->deslocamento(),
);

$titulo = 'Clientes';
$subtitulo = 'Cadastre, pesquise e edite os clientes da oficina.';
$acao = ['Novo cliente', 'formulario.php'];
$raiz = '../';
$menuAtivo = 'clientes';
require dirname(__DIR__, 2) . '/templates/cabecalho.php';
?>
<form class="cartao pesquisa" method="get">
    <div class="row g-2">
        <div class="col-md">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input class="form-control" type="search" name="pesquisa" placeholder="Pesquisar clientes"
                       value="<?= Html::e($pesquisa) ?>" aria-label="Pesquisa">
                <select class="form-select" name="filtro" aria-label="Campo da pesquisa" style="max-width: 11rem">
                    <option value="codigo" <?= Html::marcado($filtro, 'codigo') ?>>Código</option>
                    <?php foreach (ClienteRepositorio::CAMPOS_PESQUISA as $chave => [, $rotulo]): ?>
                        <option value="<?= $chave ?>" <?= Html::marcado($filtro, $chave) ?>><?= $rotulo ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-md-auto d-flex gap-2">
            <button class="btn btn-dark" type="submit">Pesquisar</button>
            <a class="btn btn-outline-secondary" href="index.php">Limpar</a>
        </div>
    </div>
</form>

<div class="cartao overflow-hidden">
    <?php if ($clientes === []): ?>
        <div class="vazio">
            <i class="bi bi-people"></i>
            Nenhum cliente encontrado.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table tabela tabela-cartoes align-middle">
                <thead>
                <tr>
                    <?= $listagem->cabecalho('id', 'Cód.') ?>
                    <?= $listagem->cabecalho('nome', 'Cliente') ?>
                    <th>Sexo</th>
                    <?= $listagem->cabecalho('nascimento', 'Nascimento') ?>
                    <th>CEP</th>
                    <th>Nº</th>
                    <th>Telefone</th>
                    <th class="text-end">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td class="codigo" data-rotulo="Cód.">#<?= $cliente->id ?></td>
                        <td data-rotulo="Cliente">
                            <span class="pessoa">
                                <span class="avatar" aria-hidden="true"><?= Html::e(mb_strtoupper(mb_substr($cliente->nome, 0, 1))) ?></span>
                                <?= Html::e($cliente->nome) ?>
                            </span>
                        </td>
                        <td data-rotulo="Sexo"><span class="etiqueta etiqueta-neutra"><?= $cliente->sexo->rotulo() ?></span></td>
                        <td data-rotulo="Nascimento"><?= Html::data($cliente->dataNascimento) ?></td>
                        <td data-rotulo="CEP"><?= Html::e($cliente->cep) ?></td>
                        <td data-rotulo="Nº"><?= Html::e($cliente->numeroCasa) ?></td>
                        <td class="text-nowrap" data-rotulo="Telefone"><?= Html::telefone($cliente->telefone) ?></td>
                        <td class="text-end text-nowrap acoes">
                            <a class="btn btn-sm btn-outline-primary" href="formulario.php?id=<?= $cliente->id ?>">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <form class="d-inline" method="post" action="excluir.php"
                                  data-confirmar="Excluir o cliente <?= Html::e($cliente->nome) ?>?">
                                <?= Pagina::campoCsrf() ?>
                                <input type="hidden" name="id" value="<?= $cliente->id ?>">
                                <button class="btn btn-sm btn-outline-danger" type="submit">
                                    <i class="bi bi-trash"></i> Excluir
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="tabela-rodape">
            <span><?= $listagem->total ?> <?= $listagem->total === 1 ? 'cliente' : 'clientes' ?></span>
            <?php require dirname(__DIR__, 2) . '/templates/paginacao.php'; ?>
        </div>
    <?php endif; ?>
</div>
<?php require dirname(__DIR__, 2) . '/templates/rodape.php'; ?>
