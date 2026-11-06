<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use Mecanica\Banco\Conexao;
use Mecanica\Repositorio\ClienteRepositorio;
use Mecanica\Repositorio\ServicoRepositorio;
use Mecanica\Web\Html;

// Totais do painel. Se o banco não estiver configurado, a página inicial continua abrindo, sem o painel.
$painel = null;
try {
    $clientes = new ClienteRepositorio(Conexao::obter());
    $servicos = new ServicoRepositorio(Conexao::obter());
    $painel = [
        'clientes' => $clientes->contar(),
        'servicos' => $servicos->contar(),
        'urgentes' => $servicos->contar('urgencia', 'comUrgencia'),
        'listaUrgentes' => $servicos->listar('urgencia', 'comUrgencia', 'id', 'desc', 5),
    ];
} catch (Throwable $erro) {
    error_log((string) $erro);
}

$titulo = 'Início';
$raiz = '';
$menuAtivo = 'inicio';
$semTitulo = true;
require dirname(__DIR__) . '/templates/cabecalho.php';

$atalhos = [
    ['Clientes', 'Cadastre, pesquise e edite os clientes da oficina.', 'clientes/index.php', 'bi-people'],
    ['Serviços', 'Registre os serviços de cada carro, com filial, mecânico e urgência.', 'servicos/index.php', 'bi-tools'],
    ['Produtos', 'Conheça a linha de insulfilmes da marca Bruago.', 'produtos/index.php', 'bi-bag'],
];
?>
<section class="destaque my-4">
    <h1>Bem-vindo à Mecânica Bruago</h1>
    <p class="mb-4">Controle clientes e serviços da oficina em um só lugar e confira os produtos da marca.</p>
    <a class="btn btn-primary btn-lg" href="servicos/formulario.php"><i class="bi bi-plus-lg me-1"></i>Novo serviço</a>
    <a class="btn btn-outline-light btn-lg" href="clientes/formulario.php">Novo cliente</a>
</section>

<?php if ($painel !== null): ?>
    <div class="row g-4 mb-4">
        <?php foreach ([
            ['clientes', 'Clientes cadastrados', 'bi-people', ''],
            ['servicos', 'Serviços registrados', 'bi-tools', ''],
            ['urgentes', 'Serviços com urgência', 'bi-exclamation-triangle', 'urgente'],
        ] as [$chave, $rotulo, $icone, $classe]): ?>
            <div class="col-sm-4">
                <div class="cartao indicador">
                    <span class="indicador-icone <?= $classe ?>"><i class="bi <?= $icone ?>"></i></span>
                    <div>
                        <div class="indicador-numero"><?= $painel[$chave] ?></div>
                        <div class="indicador-rotulo"><?= $rotulo ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <section class="cartao overflow-hidden mb-4" aria-labelledby="titulo-urgentes">
        <div class="d-flex justify-content-between align-items-center px-4 pt-4 pb-2">
            <h2 class="secao-titulo mb-0" id="titulo-urgentes">
                <i class="bi bi-exclamation-triangle"></i>Serviços urgentes
            </h2>
            <a class="small fw-semibold" href="servicos/index.php?filtro=urgencia&amp;pesquisa=comUrgencia">Ver todos</a>
        </div>
        <?php if ($painel['listaUrgentes'] === []): ?>
            <div class="vazio py-4"><i class="bi bi-check-circle"></i>Nenhum serviço urgente no momento.</div>
        <?php else: ?>
            <ul class="lista-urgentes">
                <?php foreach ($painel['listaUrgentes'] as $servico): ?>
                    <li>
                        <span class="codigo">#<?= $servico->id ?></span>
                        <span class="fw-medium flex-grow-1"><?= Html::e($servico->descricao) ?></span>
                        <span><?= Html::e($servico->carro) ?></span>
                        <span class="placa"><?= Html::e($servico->placa) ?></span>
                        <span class="text-secondary small"><?= $servico->filial->rotulo() ?></span>
                        <a class="btn btn-sm btn-outline-primary" href="servicos/formulario.php?id=<?= $servico->id ?>">
                            Abrir
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
<?php endif; ?>

<div class="row g-4">
    <?php foreach ($atalhos as [$nome, $descricao, $link, $icone]): ?>
        <div class="col-md-4">
            <a class="cartao atalho" href="<?= $link ?>">
                <span class="atalho-icone"><i class="bi <?= $icone ?>"></i></span>
                <h2><?= $nome ?></h2>
                <p><?= $descricao ?></p>
                <span class="atalho-link">Abrir <i class="bi bi-arrow-right"></i></span>
            </a>
        </div>
    <?php endforeach; ?>
</div>
<?php require dirname(__DIR__) . '/templates/rodape.php'; ?>
