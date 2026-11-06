<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/src/bootstrap.php';

use Mecanica\Banco\Conexao;
use Mecanica\Modelo\Combustivel;
use Mecanica\Modelo\Filial;
use Mecanica\Modelo\Urgencia;
use Mecanica\Repositorio\ServicoRepositorio;
use Mecanica\Validacao\ServicoValidador;
use Mecanica\Web\Html;
use Mecanica\Web\Pagina;

// Sem id: novo serviço. Com id: edição.
$repositorio = new ServicoRepositorio(Conexao::obter());
$id = Pagina::id($_GET);
$erros = [];
$valores = [
    'descricao' => '',
    'filial' => '',
    'nomeMecanico' => '',
    'carro' => '',
    'cor' => '',
    'placa' => '',
    'combustivel' => '',
    'urgencia' => '',
];

if ($id !== null) {
    $servico = $repositorio->buscar($id) ?? Pagina::naoEncontrado('Serviço não encontrado.');
    $valores = [
        'descricao' => $servico->descricao,
        'filial' => $servico->filial->value,
        'nomeMecanico' => $servico->nomeMecanico,
        'carro' => $servico->carro,
        'cor' => $servico->cor,
        'placa' => $servico->placa,
        'combustivel' => $servico->combustivel->value,
        'urgencia' => $servico->urgencia->value,
    ];
}

if (Pagina::ehPost()) {
    Pagina::exigirCsrf();
    $resultado = ServicoValidador::validar($_POST, $id);
    if ($resultado->valido()) {
        $repositorio->salvar($resultado->objeto);
        Pagina::avisar($id === null ? 'Serviço cadastrado.' : 'Serviço atualizado.');
        Pagina::redirecionar('index.php');
    }
    $valores = $resultado->valores;
    $erros = $resultado->erros;
}

$titulo = $id === null ? 'Novo serviço' : 'Editar serviço';
$subtitulo = 'Informe o serviço e os dados do carro.';
$raiz = '../';
$menuAtivo = 'servicos';
require dirname(__DIR__, 2) . '/templates/cabecalho.php';
?>
<form class="formulario" method="post" novalidate>
    <?= Pagina::campoCsrf() ?>

    <section class="cartao cartao-corpo">
    <h2 class="secao-titulo"><i class="bi bi-wrench-adjustable"></i>Serviço</h2>

    <div class="mb-3">
        <label class="form-label" for="descricao">Descrição do serviço</label>
        <input class="form-control <?= Html::classeErro($erros, 'descricao') ?>" id="descricao" name="descricao"
               maxlength="200" required autofocus value="<?= Html::e($valores['descricao']) ?>">
        <?= Html::mensagemErro($erros, 'descricao') ?>
    </div>

    <div class="row">
        <div class="col-sm-6 mb-3 mb-sm-0">
            <label class="form-label" for="filial">Filial</label>
            <select class="form-select <?= Html::classeErro($erros, 'filial') ?>" id="filial" name="filial" required>
                <option value="">Selecione</option>
                <?php foreach (Filial::cases() as $filial): ?>
                    <option value="<?= $filial->value ?>" <?= Html::marcado($valores['filial'], $filial->value) ?>>
                        <?= $filial->rotulo() ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?= Html::mensagemErro($erros, 'filial') ?>
        </div>
        <div class="col-sm-6">
            <label class="form-label" for="nomeMecanico">Mecânico responsável</label>
            <input class="form-control <?= Html::classeErro($erros, 'nomeMecanico') ?>" id="nomeMecanico"
                   name="nomeMecanico" maxlength="100" required value="<?= Html::e($valores['nomeMecanico']) ?>">
            <?= Html::mensagemErro($erros, 'nomeMecanico') ?>
        </div>
    </div>
    </section>

    <section class="cartao cartao-corpo">
    <h2 class="secao-titulo"><i class="bi bi-car-front"></i>Veículo</h2>

    <div class="row">
        <div class="col-sm-6 mb-3">
            <label class="form-label" for="carro">Carro (marca e modelo)</label>
            <input class="form-control <?= Html::classeErro($erros, 'carro') ?>" id="carro" name="carro"
                   placeholder="Ford Ka" maxlength="150" required value="<?= Html::e($valores['carro']) ?>">
            <?= Html::mensagemErro($erros, 'carro') ?>
        </div>
        <div class="col-sm-3 mb-3">
            <label class="form-label" for="cor">Cor</label>
            <input class="form-control <?= Html::classeErro($erros, 'cor') ?>" id="cor" name="cor" maxlength="100"
                   required value="<?= Html::e($valores['cor']) ?>">
            <?= Html::mensagemErro($erros, 'cor') ?>
        </div>
        <div class="col-sm-3 mb-3">
            <label class="form-label" for="placa">Placa</label>
            <input class="form-control text-uppercase <?= Html::classeErro($erros, 'placa') ?>" id="placa" name="placa" data-mascara="placa"
                   placeholder="ABC1D23" maxlength="8" required value="<?= Html::e($valores['placa']) ?>">
            <?= Html::mensagemErro($erros, 'placa') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 mb-3">
            <label class="form-label" for="combustivel">Combustível</label>
            <select class="form-select <?= Html::classeErro($erros, 'combustivel') ?>" id="combustivel"
                    name="combustivel" required>
                <option value="">Selecione</option>
                <?php foreach (Combustivel::cases() as $combustivel): ?>
                    <option value="<?= $combustivel->value ?>"
                        <?= Html::marcado($valores['combustivel'], $combustivel->value) ?>>
                        <?= $combustivel->rotulo() ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?= Html::mensagemErro($erros, 'combustivel') ?>
        </div>
        <fieldset class="col-sm-6">
            <legend class="form-label fs-6">Urgência</legend>
            <?php foreach (Urgencia::cases() as $urgencia): ?>
                <div class="form-check form-check-inline">
                    <input class="form-check-input <?= Html::classeErro($erros, 'urgencia') ?>" type="radio"
                           name="urgencia" id="urgencia-<?= $urgencia->value ?>" value="<?= $urgencia->value ?>"
                           <?= Html::marcado($valores['urgencia'], $urgencia->value, 'checked') ?>>
                    <label class="form-check-label" for="urgencia-<?= $urgencia->value ?>">
                        <?= $urgencia->rotulo() ?>
                    </label>
                </div>
            <?php endforeach; ?>
            <?= isset($erros['urgencia']) ? '<div class="text-danger small">' . Html::e($erros['urgencia']) . '</div>' : '' ?>
        </fieldset>
    </div>

    </section>

    <div class="formulario-acoes">
        <button class="btn btn-primary px-4" type="submit"><i class="bi bi-check-lg me-1"></i>Salvar</button>
        <a class="btn btn-outline-secondary" href="index.php">Cancelar</a>
    </div>
</form>
<?php require dirname(__DIR__, 2) . '/templates/rodape.php'; ?>
