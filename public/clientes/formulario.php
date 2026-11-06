<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/src/bootstrap.php';

use Mecanica\Banco\Conexao;
use Mecanica\Modelo\Sexo;
use Mecanica\Repositorio\ClienteRepositorio;
use Mecanica\Validacao\ClienteValidador;
use Mecanica\Web\Html;
use Mecanica\Web\Pagina;

// Sem id: novo cliente. Com id: edição.
$repositorio = new ClienteRepositorio(Conexao::obter());
$id = Pagina::id($_GET);
$erros = [];
$valores = ['nome' => '', 'sexo' => '', 'dataNascimento' => '', 'cep' => '', 'numeroCasa' => '', 'telefone' => ''];

if ($id !== null) {
    $cliente = $repositorio->buscar($id) ?? Pagina::naoEncontrado('Cliente não encontrado.');
    $valores = [
        'nome' => $cliente->nome,
        'sexo' => $cliente->sexo->value,
        'dataNascimento' => $cliente->dataNascimento->format('Y-m-d'),
        'cep' => $cliente->cep,
        'numeroCasa' => $cliente->numeroCasa,
        'telefone' => $cliente->telefone,
    ];
}

if (Pagina::ehPost()) {
    Pagina::exigirCsrf();
    $resultado = ClienteValidador::validar($_POST, $id);
    if ($resultado->valido()) {
        $repositorio->salvar($resultado->objeto);
        Pagina::avisar($id === null ? 'Cliente cadastrado.' : 'Cliente atualizado.');
        Pagina::redirecionar('index.php');
    }
    $valores = $resultado->valores;
    $erros = $resultado->erros;
}

$titulo = $id === null ? 'Novo cliente' : 'Editar cliente';
$subtitulo = 'Preencha os dados do cliente.';
$scripts = ['../js/cep.js'];
$raiz = '../';
$menuAtivo = 'clientes';
require dirname(__DIR__, 2) . '/templates/cabecalho.php';
?>
<form class="formulario" method="post" novalidate>
    <?= Pagina::campoCsrf() ?>

    <section class="cartao cartao-corpo">
    <h2 class="secao-titulo"><i class="bi bi-person"></i>Dados pessoais</h2>

    <div class="mb-3">
        <label class="form-label" for="nome">Nome</label>
        <input class="form-control <?= Html::classeErro($erros, 'nome') ?>" id="nome" name="nome" maxlength="100"
               required autofocus value="<?= Html::e($valores['nome']) ?>">
        <?= Html::mensagemErro($erros, 'nome') ?>
    </div>

    <fieldset class="mb-3">
        <legend class="form-label fs-6">Sexo</legend>
        <?php foreach (Sexo::cases() as $sexo): ?>
            <div class="form-check form-check-inline">
                <input class="form-check-input <?= Html::classeErro($erros, 'sexo') ?>" type="radio" name="sexo"
                       id="sexo-<?= $sexo->value ?>" value="<?= $sexo->value ?>"
                       <?= Html::marcado($valores['sexo'], $sexo->value, 'checked') ?>>
                <label class="form-check-label" for="sexo-<?= $sexo->value ?>"><?= $sexo->rotulo() ?></label>
            </div>
        <?php endforeach; ?>
        <?= isset($erros['sexo']) ? '<div class="text-danger small">' . Html::e($erros['sexo']) . '</div>' : '' ?>
    </fieldset>

    <div class="mb-0">
        <label class="form-label" for="dataNascimento">Data de nascimento</label>
        <input class="form-control <?= Html::classeErro($erros, 'dataNascimento') ?>" type="date" id="dataNascimento"
               name="dataNascimento" required value="<?= Html::e($valores['dataNascimento']) ?>">
        <?= Html::mensagemErro($erros, 'dataNascimento') ?>
    </div>
    </section>

    <section class="cartao cartao-corpo">
    <h2 class="secao-titulo"><i class="bi bi-geo-alt"></i>Endereço e contato</h2>

    <div class="row">
        <div class="col-sm-6 mb-3">
            <label class="form-label" for="cep">CEP</label>
            <input class="form-control <?= Html::classeErro($erros, 'cep') ?>" id="cep" name="cep" placeholder="00000-000" data-mascara="cep"
                   inputmode="numeric" maxlength="10" required value="<?= Html::e($valores['cep']) ?>">
            <?= Html::mensagemErro($erros, 'cep') ?>
            <div class="dica-cep" id="dica-cep" aria-live="polite"></div>
        </div>
        <div class="col-sm-6 mb-3">
            <label class="form-label" for="numeroCasa">Número da casa</label>
            <input class="form-control <?= Html::classeErro($erros, 'numeroCasa') ?>" id="numeroCasa" name="numeroCasa"
                   maxlength="10" required value="<?= Html::e($valores['numeroCasa']) ?>">
            <?= Html::mensagemErro($erros, 'numeroCasa') ?>
        </div>
    </div>

    <div class="mb-0">
        <label class="form-label" for="telefone">Telefone com DDD</label>
        <input class="form-control <?= Html::classeErro($erros, 'telefone') ?>" type="tel" id="telefone" name="telefone" data-mascara="telefone"
               placeholder="(51) 99999-9999" maxlength="16" required value="<?= Html::e($valores['telefone']) ?>">
        <?= Html::mensagemErro($erros, 'telefone') ?>
    </div>
    </section>

    <div class="formulario-acoes">
        <button class="btn btn-primary px-4" type="submit"><i class="bi bi-check-lg me-1"></i>Salvar</button>
        <a class="btn btn-outline-secondary" href="index.php">Cancelar</a>
    </div>
</form>
<?php require dirname(__DIR__, 2) . '/templates/rodape.php'; ?>
