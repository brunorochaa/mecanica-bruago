<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/src/bootstrap.php';

$titulo = 'Produtos oficiais Bruago';
$subtitulo = 'Escolha a opção e adicione ao carrinho direto aqui, ou abra o produto para ver os detalhes.';
$raiz = '../';
$menuAtivo = 'produtos';
$scripts = ['https://cdn.jsdelivr.net/npm/vue@3.5.13/dist/vue.global.prod.js', 'loja.js', 'produtos.js'];
require dirname(__DIR__, 2) . '/templates/cabecalho.php';
?>
<div id="app" v-cloak>
    <pilula-carrinho></pilula-carrinho>

    <div class="row g-4">
        <div class="col-md-6 col-xl-3" v-for="item in catalogo" :key="item.id">
            <cartao-produto :produto="item"></cartao-produto>
        </div>
    </div>

    <painel-carrinho></painel-carrinho>
</div>
<?php require dirname(__DIR__, 2) . '/templates/rodape.php'; ?>
