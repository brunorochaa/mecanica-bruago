<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/src/bootstrap.php';

// O produto é escolhido pelo id na URL (produto.php?id=insulfilme); o catálogo fica em loja.js.
$titulo = 'Produto';
$raiz = '../';
$menuAtivo = 'produtos';
$semTitulo = true;
$scripts = ['https://cdn.jsdelivr.net/npm/vue@3.5.13/dist/vue.global.prod.js', 'loja.js', 'produto.js'];
require dirname(__DIR__, 2) . '/templates/cabecalho.php';
?>
<div id="app" v-cloak>
    <div class="cabecalho-pagina d-flex flex-wrap gap-3 align-items-center justify-content-between">
        <nav aria-label="Você está em">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Produtos</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ produto ? produto.nome : 'Não encontrado' }}</li>
            </ol>
        </nav>
    </div>

    <div v-if="!produto" class="cartao vazio">
        <i class="bi bi-bag-x"></i>
        Produto não encontrado.
        <div class="mt-3"><a class="btn btn-primary" href="index.php">Ver todos os produtos</a></div>
    </div>

    <template v-else>
        <article class="cartao cartao-corpo">
            <div class="row g-4 align-items-center">
                <div class="col-md-6">
                    <imagem-opcao class="detalhe-imagem" :produto="produto" :variante="variante"></imagem-opcao>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <etiquetas-opcao :variante="variante"></etiquetas-opcao>
                    </div>
                    <h1 class="titulo mb-2">{{ produto.nome }}</h1>
                    <p class="text-secondary"><texto-destacado :texto="produto.descricao"></texto-destacado></p>

                    <p class="produto-preco" :class="{ 'fora-de-estoque': !emEstoque(variante) }">
                        <span v-if="emPromocao(variante)" class="preco-antigo">{{ moeda(variante.preco) }}</span>
                        <span :class="{ 'preco-promocional': emPromocao(variante) }">{{ moeda(precoFinal(variante)) }}</span>
                    </p>

                    <ul class="produto-lista">
                        <li v-for="detalhe in produto.detalhes" :key="detalhe" class="etiqueta etiqueta-neutra">{{ detalhe }}</li>
                    </ul>

                    <p class="form-label mb-2">{{ produto.tipoOpcao }}: <strong>{{ variante.rotulo }}</strong></p>
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <button v-for="(item, indice) in produto.variantes" :key="item.id" type="button"
                                :class="[item.cor ? 'cor-variante' : 'opcao-variante', { selecionada: indice === selecionada, esgotada: item.quantidade === 0 }]"
                                :style="item.cor ? { backgroundColor: item.cor } : null"
                                :aria-label="item.rotulo" :title="item.rotulo"
                                :aria-pressed="indice === selecionada" @click="selecionada = indice">{{ item.cor ? '' : item.rotulo }}</button>
                    </div>
                    <p class="small text-secondary mb-4">
                        <template v-if="!emEstoque(variante)">Esta opção está esgotada.</template>
                        <template v-else-if="restantes === 0">Você já colocou todo o estoque no carrinho.</template>
                        <template v-else>{{ restantes }} {{ restantes === 1 ? 'unidade disponível' : 'unidades disponíveis' }}</template>
                    </p>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-primary btn-lg" :disabled="!podeAdicionar"
                                @click="carrinho.adicionar(variante.id)">
                            <i class="bi bi-cart-plus me-1"></i>{{ emEstoque(variante) ? 'Adicionar ao carrinho' : 'Indisponível' }}
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-lg" :disabled="!emEstoque(variante)"
                                @click="carrinho.comprarAgora(variante.id)">
                            <i class="bi bi-lightning-charge me-1"></i>Comprar agora
                        </button>
                    </div>
                    <div v-if="noCarrinho" class="mt-3 small">
                        <i class="bi bi-cart-check text-success me-1"></i>{{ noCarrinho }} no carrinho &middot;
                        <a href="carrinho.php">Ver carrinho</a> &middot;
                        <button type="button" class="btn btn-link btn-sm p-0 text-secondary align-baseline"
                                @click="carrinho.remover(variante.id)">remover uma unidade</button>
                    </div>
                </div>
            </div>
        </article>

        <p class="mt-4"><a href="index.php"><i class="bi bi-arrow-left me-1"></i>Voltar para os produtos</a></p>
    </template>
    <painel-carrinho></painel-carrinho>
</div>
<?php require dirname(__DIR__, 2) . '/templates/rodape.php'; ?>
