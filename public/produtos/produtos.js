// Página com todos os produtos: escolha rápida da opção e botão de adicionar direto no cartão.
const { createApp } = Vue;

const CartaoProduto = {
    components: componentesComuns,
    props: { produto: { type: Object, required: true } },
    data() {
        // Já abre na primeira opção com estoque (ou na primeira, se tudo estiver esgotado).
        return { selecionada: Math.max(0, this.produto.variantes.findIndex(emEstoque)), carrinho };
    },
    setup: () => ({ moeda, precoFinal, emEstoque, emPromocao }),
    computed: {
        variante() {
            return this.produto.variantes[this.selecionada];
        },
        restantes() {
            return this.carrinho.restantes(this.variante.id);
        },
        noCarrinho() {
            return this.carrinho.quantidade(this.variante.id);
        },
        textoBotao() {
            if (!emEstoque(this.variante)) {
                return 'Esgotado';
            }
            return this.restantes > 0 ? 'Adicionar' : 'Todo o estoque no carrinho';
        },
    },
    template: `
        <article class="cartao produto-cartao produto-vitrine h-100">
            <a class="produto-link" :href="'produto.php?id=' + produto.id"
               :aria-label="'Ver ' + produto.nome">
                <imagem-opcao :produto="produto" :variante="variante"></imagem-opcao>
            </a>
            <div class="produto-corpo">
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <etiquetas-opcao :variante="variante"></etiquetas-opcao>
                </div>
                <h2 class="h5 fw-bold mb-1">
                    <a class="produto-link" :href="'produto.php?id=' + produto.id">{{ produto.nome }}</a>
                </h2>
                <p class="text-secondary small mb-3"><texto-destacado :texto="produto.descricao"></texto-destacado></p>

                <div class="mt-auto">
                    <p class="form-label small mb-2">{{ produto.tipoOpcao }}: <strong>{{ variante.rotulo }}</strong></p>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <button v-for="(item, indice) in produto.variantes" :key="item.id" type="button"
                                :class="[item.cor ? 'cor-variante cor-pequena' : 'opcao-variante opcao-pequena',
                                         { selecionada: indice === selecionada, esgotada: item.quantidade === 0 }]"
                                :style="item.cor ? { backgroundColor: item.cor } : null"
                                :aria-label="item.rotulo + (item.quantidade === 0 ? ' (esgotado)' : '')"
                                :aria-pressed="indice === selecionada" :title="item.rotulo"
                                @click="selecionada = indice">{{ item.cor ? '' : item.rotulo }}</button>
                    </div>

                    <p class="produto-preco mb-3" :class="{ 'fora-de-estoque': !emEstoque(variante) }">
                        <span v-if="emPromocao(variante)" class="preco-antigo">{{ moeda(variante.preco) }}</span>
                        <span :class="{ 'preco-promocional': emPromocao(variante) }">{{ moeda(precoFinal(variante)) }}</span>
                    </p>

                    <button type="button" class="btn btn-primary w-100" :disabled="restantes <= 0"
                            :aria-label="textoBotao + ': ' + produto.nome + ' ' + variante.rotulo"
                            @click="carrinho.adicionar(variante.id)">
                        <i class="bi bi-cart-plus me-1"></i>{{ textoBotao }}
                        <span v-if="noCarrinho" class="badge text-bg-light ms-1">{{ noCarrinho }} no carrinho</span>
                    </button>
                    <button type="button" class="btn btn-outline-primary w-100 mt-2" :disabled="!emEstoque(variante) || (restantes <= 0 && noCarrinho === 0)"
                            @click="carrinho.comprarAgora(variante.id)">
                        <i class="bi bi-lightning-charge me-1"></i>Comprar agora
                    </button>
                </div>
            </div>
        </article>
    `,
};

createApp({
    components: { ...componentesComuns, CartaoProduto },
    data() {
        return { catalogo: PRODUTOS };
    },
}).mount('#app');
