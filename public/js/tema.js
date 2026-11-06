// Tema claro/escuro. Carregado no <head> para aplicar o tema antes de pintar a página (sem piscar).
(function () {
    const chave = 'tema-mecanica';
    // ?tema=claro ou ?tema=escuro na URL força o tema (e fica salvo).
    const pedido = new URLSearchParams(window.location.search).get('tema');
    if (pedido === 'claro' || pedido === 'escuro') {
        localStorage.setItem(chave, pedido === 'escuro' ? 'dark' : 'light');
    }
    const salvo = localStorage.getItem(chave);
    const preferido = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    const raiz = document.documentElement;
    raiz.setAttribute('data-bs-theme', salvo || preferido);

    document.addEventListener('DOMContentLoaded', function () {
        const botao = document.getElementById('botao-tema');
        if (!botao) {
            return;
        }
        const atualizar = function () {
            const escuro = raiz.getAttribute('data-bs-theme') === 'dark';
            botao.querySelector('i').className = 'bi ' + (escuro ? 'bi-sun' : 'bi-moon-stars');
            botao.setAttribute('aria-label', escuro ? 'Usar tema claro' : 'Usar tema escuro');
        };
        botao.addEventListener('click', function () {
            const novo = raiz.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            raiz.setAttribute('data-bs-theme', novo);
            localStorage.setItem(chave, novo);
            atualizar();
        });
        atualizar();
    });
})();
