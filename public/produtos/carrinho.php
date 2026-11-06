<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/src/bootstrap.php';

$titulo = 'Carrinho';
$subtitulo = 'Confira os itens, ajuste as quantidades e finalize o pedido.';
$raiz = '../';
$menuAtivo = 'produtos';
$scripts = ['https://cdn.jsdelivr.net/npm/vue@3.5.13/dist/vue.global.prod.js', 'loja.js', 'carrinho.js'];
require dirname(__DIR__, 2) . '/templates/cabecalho.php';
?>
<div id="app" v-cloak>
    <!-- Pedido finalizado -->
    <section v-if="pedido" class="cartao cartao-corpo pedido-ok" aria-live="polite">
        <div class="text-center mb-4">
            <span class="pedido-icone"><i class="bi bi-check-lg"></i></span>
            <h2 class="h3 fw-bold mt-3">Pedido realizado!</h2>
            <p class="text-secondary mb-0">
                Número do pedido: <strong>{{ pedido.numero }}</strong><br>
                Retirada na oficina, sem custo de entrega.
            </p>
        </div>
        <ul class="lista-carrinho">
            <li v-for="linha in pedido.linhas" :key="linha.id">
                <span class="flex-grow-1">
                    {{ linha.nome }} <span class="text-secondary">&middot; {{ linha.rotulo }}</span>
                </span>
                <span class="text-secondary">{{ linha.quantidade }}x</span>
                <span class="fw-semibold">{{ moeda(linha.subtotal) }}</span>
            </li>
            <li class="total">
                <span class="flex-grow-1">Total</span>
                <span>{{ moeda(pedido.total) }}</span>
            </li>
        </ul>
        <p v-if="pedido.economia > 0" class="text-center economia mt-3 mb-0">
            Você economizou {{ moeda(pedido.economia) }} nas promoções.
        </p>
        <p class="small text-secondary text-center mt-4 mb-3">
            Pedido de demonstração: nenhum pagamento foi feito e nada foi enviado para a oficina.
        </p>
        <div class="text-center">
            <a class="btn btn-primary" href="index.php"><i class="bi bi-arrow-left me-1"></i>Voltar aos produtos</a>
        </div>
    </section>

    <!-- Carrinho vazio -->
    <section v-else-if="!carrinho.ids.length" class="cartao vazio">
        <i class="bi bi-cart-x"></i>
        <p class="mb-3">Seu carrinho está vazio.</p>
        <a class="btn btn-primary" href="index.php">Ver produtos</a>
    </section>

    <!-- Carrinho com itens -->
    <div v-else class="row g-4 align-items-start">
        <div class="col-lg-8">
            <section class="cartao overflow-hidden" aria-labelledby="titulo-itens">
                <div class="itens-cabecalho">
                    <h2 class="h6 mb-0" id="titulo-itens">
                        {{ carrinho.ids.length }} {{ carrinho.ids.length === 1 ? 'item' : 'itens' }}
                    </h2>
                    <button type="button" class="btn btn-link btn-sm text-secondary p-0" @click="carrinho.esvaziar()">
                        <i class="bi bi-trash3"></i> Esvaziar carrinho
                    </button>
                </div>
                <ul class="itens-loja">
                    <li v-for="linha in carrinho.linhas" :key="linha.id">
                        <a :href="'produto.php?id=' + linha.produto" :aria-label="'Ver ' + linha.nome">
                            <imagem-opcao class="miniatura" :produto="linha.produtoObj" :variante="linha.varianteObj"></imagem-opcao>
                        </a>
                        <div class="item-info">
                            <a class="fw-semibold" :href="'produto.php?id=' + linha.produto">{{ linha.nome }}</a>
                            <div class="small text-secondary">
                                <span v-if="linha.cor" class="amostra-cor" :style="{ backgroundColor: linha.cor }"></span>{{ linha.rotulo }}
                            </div>
                            <div class="small mt-1">
                                <span v-if="linha.promocao" class="preco-antigo me-1">{{ moeda(linha.precoOriginal) }}</span>
                                <span :class="{ 'preco-promocional fw-semibold': linha.promocao }">{{ moeda(linha.precoUnitario) }}</span>
                                <span class="text-secondary"> cada</span>
                            </div>
                            <div class="quantidade mt-2" role="group" :aria-label="'Quantidade de ' + linha.nome + ' ' + linha.rotulo">
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                        :aria-label="'Diminuir a quantidade de ' + linha.nome + ' ' + linha.rotulo"
                                        @click="carrinho.remover(linha.id)"><i class="bi bi-dash-lg"></i></button>
                                <span class="quantidade-valor" aria-live="polite">{{ linha.quantidade }}</span>
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                        :disabled="linha.quantidade >= linha.maximo"
                                        :aria-label="'Aumentar a quantidade de ' + linha.nome + ' ' + linha.rotulo"
                                        @click="carrinho.adicionar(linha.id)"><i class="bi bi-plus-lg"></i></button>
                                <small v-if="linha.quantidade >= linha.maximo" class="text-secondary ms-2">Estoque máximo</small>
                            </div>
                        </div>
                        <div class="item-total text-end">
                            <div class="fw-bold">{{ moeda(linha.subtotal) }}</div>
                            <button type="button" class="btn btn-link btn-sm p-0 text-secondary mt-2"
                                    :aria-label="'Remover ' + linha.nome + ' ' + linha.rotulo + ' do carrinho'"
                                    @click="carrinho.remover(linha.id, true)"><i class="bi bi-trash3"></i> Remover</button>
                        </div>
                    </li>
                </ul>
            </section>
            <p class="mt-3 mb-0"><a href="index.php"><i class="bi bi-arrow-left me-1"></i>Continuar comprando</a></p>
        </div>

        <aside class="col-lg-4">
            <div class="cartao cartao-corpo resumo" aria-labelledby="titulo-resumo">
                <h2 class="secao-titulo" id="titulo-resumo"><i class="bi bi-receipt"></i>Resumo do pedido</h2>
                <dl class="resumo-linhas">
                    <div>
                        <dt>Produtos ({{ carrinho.ids.length }})</dt>
                        <dd>{{ moeda(carrinho.totalSemDesconto) }}</dd>
                    </div>
                    <div v-if="carrinho.economia > 0" class="economia">
                        <dt>Descontos</dt>
                        <dd>&minus; {{ moeda(carrinho.economia) }}</dd>
                    </div>
                    <div>
                        <dt>Retirada na oficina</dt>
                        <dd class="economia">Grátis</dd>
                    </div>
                    <div class="resumo-total">
                        <dt>Total</dt>
                        <dd>{{ moeda(carrinho.total) }}</dd>
                    </div>
                </dl>
                <button type="button" class="btn btn-primary btn-lg w-100" @click="finalizar()">
                    <i class="bi bi-bag-check me-1"></i>Finalizar compra
                </button>
                <p class="small text-secondary text-center mt-3 mb-0">
                    <i class="bi bi-info-circle"></i> Demonstração: não há pagamento real.
                </p>
            </div>
        </aside>
    </div>
</div>
<?php require dirname(__DIR__, 2) . '/templates/rodape.php'; ?>
