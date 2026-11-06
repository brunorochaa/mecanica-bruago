<?php

declare(strict_types=1);

use Mecanica\Web\Html;
use Mecanica\Web\Pagina;

/**
 * @var string $titulo título da página
 * @var string $raiz caminho até a pasta public ('' ou '../')
 * @var string $menuAtivo 'inicio', 'clientes', 'servicos' ou 'produtos'
 * @var string|null $subtitulo texto abaixo do título (opcional)
 * @var array{0: string, 1: string}|null $acao botão ao lado do título: [texto, link] (opcional)
 * @var bool|null $semTitulo true para a página desenhar o próprio título (opcional)
 */

$itensMenu = [
    'inicio' => ['Início', 'index.php', 'bi-house-door'],
    'clientes' => ['Clientes', 'clientes/index.php', 'bi-people'],
    'servicos' => ['Serviços', 'servicos/index.php', 'bi-tools'],
    'produtos' => ['Produtos', 'produtos/index.php', 'bi-bag'],
];
$aviso = Pagina::tirarAviso();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::e($titulo) ?> | Mecânica Bruago</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/inter@5.0.18/index.min.css">
    <link rel="stylesheet" href="<?= $raiz ?>css/estilo.css">
    <script src="<?= $raiz ?>js/tema.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark barra">
    <div class="container">
        <a class="navbar-brand" href="<?= $raiz ?>index.php">
            <span class="marca-icone"><i class="bi bi-gear-fill"></i></span>Mecânica Bruago
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu"
                aria-controls="menu" aria-expanded="false" aria-label="Abrir menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto gap-lg-1 pt-2 pt-lg-0">
                <?php foreach ($itensMenu as $chave => [$rotulo, $link, $icone]): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $chave === $menuAtivo ? 'active' : '' ?>"
                           <?= $chave === $menuAtivo ? 'aria-current="page"' : '' ?>
                           href="<?= $raiz . $link ?>"><i class="bi <?= $icone ?>"></i><?= $rotulo ?></a>
                    </li>
                <?php endforeach; ?>
                <li class="nav-item">
                    <button class="botao-tema" type="button" id="botao-tema" aria-label="Usar tema escuro">
                        <i class="bi bi-moon-stars"></i>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>
<main class="container pb-5">
    <?php if (empty($semTitulo)): ?>
        <header class="cabecalho-pagina d-flex flex-wrap gap-3 align-items-center justify-content-between">
            <div>
                <h1 class="titulo"><?= Html::e($titulo) ?></h1>
                <?php if (!empty($subtitulo)): ?>
                    <p class="subtitulo"><?= Html::e($subtitulo) ?></p>
                <?php endif; ?>
            </div>
            <?php if (!empty($acao)): ?>
                <a class="btn btn-primary" href="<?= Html::e($acao[1]) ?>">
                    <i class="bi bi-plus-lg me-1"></i><?= Html::e($acao[0]) ?>
                </a>
            <?php endif; ?>
        </header>
    <?php endif; ?>
    <?php if ($aviso !== null): ?>
        <div class="alert alert-success d-flex align-items-center gap-2 <?= empty($semTitulo) ? '' : 'mt-4' ?>"
             role="status">
            <i class="bi bi-check-circle-fill"></i><?= Html::e($aviso) ?>
        </div>
    <?php endif; ?>
