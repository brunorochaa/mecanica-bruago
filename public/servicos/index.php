<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/src/bootstrap.php';

use Mecanica\Banco\Conexao;
use Mecanica\Modelo\Urgencia;
use Mecanica\Repositorio\ServicoRepositorio;
use Mecanica\Web\Html;
use Mecanica\Web\Listagem;
use Mecanica\Web\Pagina;

$filtro = (string) ($_GET['filtro'] ?? '');
$pesquisa = (string) ($_GET['pesquisa'] ?? '');
$repositorio = new ServicoRepositorio(Conexao::obter());
$listagem = Listagem::ler($_GET, array_keys(ServicoRepositorio::ORDENAVEIS), 'id', 'desc')
    ->comTotal($repositorio->contar($filtro, $pesquisa));
$servicos = $repositorio->listar(
    $filtro,
    $pesquisa,
    $listagem->ordem,
    $listagem->direcao,
    Listagem::POR_PAGINA,
    $listagem->deslocamento(),
);

$titulo = 'Serviços';
$subtitulo = 'Acompanhe os serviços de cada carro, com filial, mecânico e urgência.';
$acao = ['Novo serviço', 'formulario.php'];
$raiz = '../';
$menuAtivo = 'servicos';
require dirname(__DIR__, 2) . '/templates/cabecalho.php';
?>
<form class="cartao pesquisa" method="get">
    <div class="row g-2">
        <div class="col-md">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input class="form-control" type="search" name="pesquisa" placeholder="Pesquisar serviços"
                       value="<?= Html::e($pesquisa) ?>" aria-label="Pesquisa">
                <select class="form-select" name="filtro" aria-label="Campo da pesquisa" style="max-width: 11rem">
                    <option value="codigo" <?= Html::marcado($filtro, 'codigo') ?>>Código</option>
                    <?php foreach (ServicoRepositorio::CAMPOS_PESQUISA as $chave => [, $rotulo]): ?>
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
    <?php if ($servicos === []): ?>
        <div class="vazio">
            <i class="bi bi-tools"></i>
            Nenhum serviço encontrado.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table tabela tabela-cartoes align-middle">
                <thead>
                <tr>
                    <?= $listagem->cabecalho('id', 'Cód.') ?>
                    <?= $listagem->cabecalho('descricao', 'Serviço') ?>
                    <?= $listagem->cabecalho('carro', 'Carro') ?>
                    <th>Placa</th>
                    <?= $listagem->cabecalho('filial', 'Filial') ?>
                    <th>Mecânico</th>
                    <?= $listagem->cabecalho('urgencia', 'Urgência') ?>
                    <th class="text-end">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($servicos as $servico): ?>
                    <tr>
                        <td class="codigo" data-rotulo="Cód.">#<?= $servico->id ?></td>
                        <td class="fw-medium" data-rotulo="Serviço"><?= Html::e($servico->descricao) ?></td>
                        <td data-rotulo="Carro">
                            <div>
                                <?= Html::e($servico->carro) ?>
                                <div class="small text-secondary">
                                    <?= Html::e($servico->cor) ?> &middot; <?= $servico->combustivel->rotulo() ?>
                                </div>
                            </div>
                        </td>
                        <td data-rotulo="Placa"><span class="placa"><?= Html::e($servico->placa) ?></span></td>
                        <td class="text-nowrap" data-rotulo="Filial"><?= $servico->filial->rotulo() ?></td>
                        <td data-rotulo="Mecânico"><?= Html::e($servico->nomeMecanico) ?></td>
                        <td data-rotulo="Urgência">
                            <?php if ($servico->urgencia === Urgencia::ComUrgencia): ?>
                                <span class="etiqueta etiqueta-urgente">
                                    <i class="bi bi-exclamation-circle-fill"></i><?= $servico->urgencia->rotulo() ?>
                                </span>
                            <?php else: ?>
                                <span class="etiqueta etiqueta-normal"><?= $servico->urgencia->rotulo() ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end text-nowrap acoes">
                            <a class="btn btn-sm btn-outline-primary" href="formulario.php?id=<?= $servico->id ?>">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <form class="d-inline" method="post" action="excluir.php"
                                  data-confirmar="Excluir o serviço nº <?= $servico->id ?>?">
                                <?= Pagina::campoCsrf() ?>
                                <input type="hidden" name="id" value="<?= $servico->id ?>">
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
            <span><?= $listagem->total ?> <?= $listagem->total === 1 ? 'serviço' : 'serviços' ?></span>
            <?php require dirname(__DIR__, 2) . '/templates/paginacao.php'; ?>
        </div>
    <?php endif; ?>
</div>
<?php require dirname(__DIR__, 2) . '/templates/rodape.php'; ?>
