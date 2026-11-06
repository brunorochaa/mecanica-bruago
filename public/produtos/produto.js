// Página de um produto (o id vem na URL: produto.php?id=insulfilme).
const { createApp } = Vue;

const idPedido = new URLSearchParams(window.location.search).get('id');

createApp({
    components: componentesComuns,
    data() {
        const produto = PRODUTOS.find((item) => item.id === idPedido) ?? null;
        if (produto) {
            document.title = produto.nome + ' | Mecânica Bruago';
        }
        // Já abre na primeira opção com estoque.
        const primeira = produto ? Math.max(0, produto.variantes.findIndex(emEstoque)) : 0;
        return { produto, selecionada: primeira, carrinho };
    },
    computed: {
        variante() {
            return this.produto.variantes[this.selecionada];
        },
        noCarrinho() {
            return this.carrinho.quantidade(this.variante.id);
        },
        podeAdicionar() {
            return this.variante.quantidade - this.noCarrinho > 0;
        },
        restantes() {
            return this.variante.quantidade - this.noCarrinho;
        },
    },
    methods: { moeda, precoFinal, emEstoque, emPromocao },
}).mount('#app');
