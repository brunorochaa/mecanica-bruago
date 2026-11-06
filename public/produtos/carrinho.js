// Página do carrinho (estilo loja): itens, resumo do pedido e finalizar compra.
// Não há pagamento nem servidor: o pedido é só uma demonstração e o carrinho fica no navegador.
const { createApp } = Vue;

createApp({
    components: { ImagemOpcao },
    data() {
        return { carrinho, pedido: null };
    },
    methods: {
        moeda,
        finalizar() {
            if (!carrinho.ids.length) {
                return;
            }
            this.pedido = {
                numero: 'BR-' + Date.now().toString().slice(-6),
                itens: carrinho.ids.length,
                linhas: carrinho.linhas.map((linha) => ({ ...linha })),
                total: carrinho.total,
                economia: carrinho.economia,
            };
            carrinho.esvaziar();
            window.scrollTo({ top: 0 });
        },
    },
}).mount('#app');
