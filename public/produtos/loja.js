// Parte comum das páginas de produtos (Vue 3, sem etapa de build): catálogo, carrinho e componentes.
// Carregue depois do Vue e antes de vitrine.js ou produto.js.
const { reactive, watch } = Vue;

const moeda = (valor) => valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

// Catálogo. Cada opção (cor, volume...) tem preço, estoque e, se houver, preço promocional.
// Trechos entre { } na descrição aparecem em destaque.
const PRODUTOS = [
    {
        id: 'insulfilme',
        nome: 'Bruago Insulfilme',
        descricao: 'Película de escurecimento com filtro de {99% dos raios UV}.',
        icone: 'bi-sun',
        detalhes: ['Sem película', 'G-35%', 'G-5%'],
        tipoOpcao: 'Cor da película',
        variantes: [
            { id: 2234, ajuste: 'preencher', rotulo: 'Incolor', cor: '#e8f1f0', imagem: 'imagens/1.png', quantidade: 100, preco: 400, precoPromocional: 320 },
            { id: 2235, ajuste: 'preencher', rotulo: 'Fumê', cor: '#4f5555', imagem: 'imagens/2.png', quantidade: 0, preco: 400, precoPromocional: null },
            { id: 2236, ajuste: 'preencher', rotulo: 'Preto', cor: '#110f12', imagem: 'imagens/3.png', quantidade: 20, preco: 400, precoPromocional: null },
        ],
    },
    {
        id: 'rodas',
        nome: 'Bruago Roda Esportiva',
        descricao: 'Jogo com 4 rodas de liga leve aro 17, com {20% menos peso} que a roda comum.',
        icone: 'bi-record-circle',
        ilustracao: 'roda',
        detalhes: ['Aro 17', 'Liga leve', '4 furos'],
        tipoOpcao: 'Acabamento',
        variantes: [
            { id: 3101, rotulo: 'Grafite', cor: '#3b3f46', imagem: 'imagens/roda-grafite.png', quantidade: 8, preco: 2190, precoPromocional: null },
            { id: 3102, rotulo: 'Prata', cor: '#c3c8cf', imagem: 'imagens/roda-prata.png', quantidade: 5, preco: 2190, precoPromocional: 1890 },
            { id: 3103, rotulo: 'Preto fosco', cor: '#18181b', imagem: 'imagens/roda-preto-fosco.png', quantidade: 0, preco: 2290, precoPromocional: null },
        ],
    },
    {
        id: 'oleo',
        nome: 'Ipiranga F1 Master Sintético 5W-30',
        descricao: 'Proteção do motor com {até 10.000 km} entre as trocas e menos desgaste.',
        icone: 'bi-droplet-half',
        ilustracao: 'oleo',
        detalhes: ['100% sintético', '5W-30', 'API SP'],
        tipoOpcao: 'Volume',
        variantes: [
            { id: 4101, rotulo: '1 litro', imagem: 'imagens/oleo-1l.webp', quantidade: 60, preco: 49.9, precoPromocional: null },
            { id: 4102, rotulo: '4 litros', imagem: 'imagens/oleo-4l.webp', quantidade: 25, preco: 179.9, precoPromocional: 149.9 },
        ],
    },
    {
        id: 'palhetas',
        nome: 'Bruago Palheta de Limpador',
        descricao: 'Borracha de silicone que dura {2x mais} e não deixa riscos no vidro.',
        icone: 'bi-cloud-rain',
        ilustracao: 'palheta',
        detalhes: ['Silicone', 'Par', 'Aerodinâmica'],
        tipoOpcao: 'Tamanho',
        variantes: [
            { id: 5101, rotulo: '18 polegadas', imagem: 'imagens/palheta-18.webp', quantidade: 30, preco: 64.9, precoPromocional: null },
            { id: 5102, rotulo: '20 polegadas', imagem: 'imagens/palheta-20.webp', quantidade: 12, preco: 69.9, precoPromocional: null },
            { id: 5103, rotulo: '22 polegadas', imagem: 'imagens/palheta-22.webp', quantidade: 0, preco: 74.9, precoPromocional: null },
        ],
    },
];

const precoFinal = (variante) => variante.precoPromocional ?? variante.preco;
const emEstoque = (variante) => variante.quantidade > 0;
// Só é promoção de verdade se a opção tem preço promocional e ainda há estoque.
const emPromocao = (variante) => emEstoque(variante) && variante.precoPromocional !== null;
const desconto = (variante) => (emPromocao(variante)
    ? Math.round((1 - variante.precoPromocional / variante.preco) * 100)
    : 0);

// Quebra "texto {destaque} texto" em partes, para destacar sem usar v-html.
const partes = (texto) => texto.split(/(\{[^}]+\})/).filter(Boolean).map((parte) => (
    parte.startsWith('{')
        ? { texto: parte.slice(1, -1), destaque: true }
        : { texto: parte, destaque: false }
));

function acharOpcao(id) {
    for (const produto of PRODUTOS) {
        const variante = produto.variantes.find((item) => item.id === id);
        if (variante) {
            return { produto, variante };
        }
    }
    return null;
}

// Carrinho guardado no navegador (localStorage), para continuar ao trocar de página.
const CHAVE_CARRINHO = 'carrinho-bruago';

function lerCarrinho() {
    try {
        const ids = JSON.parse(localStorage.getItem(CHAVE_CARRINHO) || '[]');
        if (!Array.isArray(ids)) {
            return [];
        }
        // Ignora opções que não existem mais e respeita o estoque atual.
        const contagem = new Map();
        return ids.filter((id) => {
            const opcao = acharOpcao(id);
            const usados = contagem.get(id) ?? 0;
            if (opcao === null || usados >= opcao.variante.quantidade) {
                return false;
            }
            contagem.set(id, usados + 1);
            return true;
        });
    } catch (erro) {
        return [];
    }
}

let temporizadorAviso = null;

const carrinho = reactive({
    ids: lerCarrinho(),
    // Último item adicionado, mostrado no aviso (toast) por alguns segundos.
    aviso: null,
    quantidade(id) {
        return this.ids.filter((item) => item === id).length;
    },
    // Quantas unidades ainda cabem no carrinho (estoque menos o que já está nele).
    restantes(id) {
        const opcao = acharOpcao(id);
        return opcao ? opcao.variante.quantidade - this.quantidade(id) : 0;
    },
    adicionar(id) {
        const opcao = acharOpcao(id);
        if (!opcao || this.restantes(id) <= 0) {
            return false;
        }
        this.ids.push(id);
        this.aviso = { nome: opcao.produto.nome, rotulo: opcao.variante.rotulo, quantidade: this.quantidade(id) };
        clearTimeout(temporizadorAviso);
        temporizadorAviso = setTimeout(() => { this.aviso = null; }, 4000);
        return true;
    },
    // Põe no carrinho (se ainda não estiver) e vai direto para a página do carrinho.
    comprarAgora(id) {
        if (this.quantidade(id) === 0 && !this.adicionar(id)) {
            return;
        }
        // Salva já: a página muda antes de o watch gravar.
        localStorage.setItem(CHAVE_CARRINHO, JSON.stringify(this.ids));
        window.location.href = 'carrinho.php';
    },
    fecharAviso() {
        clearTimeout(temporizadorAviso);
        this.aviso = null;
    },
    // Remove uma unidade da opção.
    // Com tudo = true, remove todas as unidades da opção.
    remover(id, tudo = false) {
        do {
            const indice = this.ids.lastIndexOf(id);
            if (indice === -1) {
                return;
            }
            this.ids.splice(indice, 1);
        } while (tudo);
    },
    esvaziar() {
        this.ids = [];
    },
    // Uma linha por opção, com quantidade e subtotal.
    get linhas() {
        const quantidades = new Map();
        this.ids.forEach((id) => quantidades.set(id, (quantidades.get(id) ?? 0) + 1));
        return [...quantidades].map(([id, quantidade]) => {
            const { produto, variante } = acharOpcao(id);
            return {
                id,
                quantidade,
                produto: produto.id,
                nome: produto.nome,
                rotulo: variante.rotulo,
                cor: variante.cor ?? null,
                maximo: variante.quantidade,
                produtoObj: produto,
                varianteObj: variante,
                precoUnitario: precoFinal(variante),
                precoOriginal: variante.preco,
                promocao: variante.precoPromocional !== null,
                subtotal: precoFinal(variante) * quantidade,
            };
        });
    },
    get total() {
        return this.linhas.reduce((soma, linha) => soma + linha.subtotal, 0);
    },
    // Total sem os descontos de promoção, e quanto o cliente economiza.
    get totalSemDesconto() {
        return this.linhas.reduce((soma, linha) => soma + linha.precoOriginal * linha.quantidade, 0);
    },
    get economia() {
        return this.totalSemDesconto - this.total;
    },
});

watch(() => [...carrinho.ids], (ids) => localStorage.setItem(CHAVE_CARRINHO, JSON.stringify(ids)));

// Botão do carrinho no topo da página.
const PilulaCarrinho = {
    setup: () => ({ carrinho, moeda }),
    template: `
        <button type="button" class="carrinho" data-bs-toggle="offcanvas" data-bs-target="#painel-carrinho"
                aria-controls="painel-carrinho"
                :aria-label="'Abrir o carrinho: ' + carrinho.ids.length + (carrinho.ids.length === 1 ? ' item' : ' itens')">
            <i class="bi bi-cart3"></i>Carrinho <span class="carrinho-contador">{{ carrinho.ids.length }}</span>
            <span v-if="carrinho.ids.length">&middot; {{ moeda(carrinho.total) }}</span>
        </button>
    `,
};

// Carrinho em painel lateral (offcanvas do Bootstrap), mais o aviso de "adicionado".
// Fica uma vez em cada página; a pílula do topo abre o painel pelos atributos data-bs-*.
const PainelCarrinho = {
    setup: () => ({ carrinho, moeda }),
    template: `
        <div class="offcanvas offcanvas-end painel-carrinho" tabindex="-1" id="painel-carrinho"
             aria-labelledby="titulo-carrinho">
            <div class="offcanvas-header">
                <h2 class="offcanvas-title h5 mb-0" id="titulo-carrinho"><i class="bi bi-cart3 me-2"></i>Seu carrinho</h2>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fechar o carrinho"></button>
            </div>
            <div class="offcanvas-body d-flex flex-column">
                <div v-if="!carrinho.ids.length" class="carrinho-vazio">
                    <i class="bi bi-cart-x"></i>
                    <p class="mb-3">O carrinho está vazio.</p>
                    <a href="index.php" class="btn btn-outline-secondary btn-sm">Ver produtos</a>
                </div>
                <ul v-else class="lista-carrinho flex-grow-1">
                    <li v-for="linha in carrinho.linhas" :key="linha.id" class="item-carrinho">
                        <div class="flex-grow-1">
                            <a class="fw-medium" :href="'produto.php?id=' + linha.produto">{{ linha.nome }}</a>
                            <div class="small text-secondary">
                                <span v-if="linha.cor" class="amostra-cor" :style="{ backgroundColor: linha.cor }"></span>{{ linha.rotulo }}
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
                        <div class="text-end">
                            <div class="fw-semibold">{{ moeda(linha.subtotal) }}</div>
                            <button type="button" class="btn btn-link btn-sm p-0 text-secondary"
                                    :aria-label="'Remover ' + linha.nome + ' ' + linha.rotulo + ' do carrinho'"
                                    @click="carrinho.remover(linha.id, true)"><i class="bi bi-trash3"></i> Remover</button>
                        </div>
                    </li>
                </ul>
                <div v-if="carrinho.ids.length" class="rodape-carrinho">
                    <div class="d-flex justify-content-between fs-5 fw-bold mb-3">
                        <span>Total</span><span>{{ moeda(carrinho.total) }}</span>
                    </div>
                    <a href="carrinho.php" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-bag-check me-1"></i>Ir para o carrinho
                    </a>
                    <button type="button" class="btn btn-outline-secondary w-100" @click="carrinho.esvaziar()">
                        <i class="bi bi-trash3 me-1"></i>Esvaziar carrinho
                    </button>
                </div>
            </div>
        </div>

        <div class="aviso-carrinho" aria-live="polite" aria-atomic="true">
            <div v-if="carrinho.aviso" class="toast show align-items-center" role="status">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle-fill text-success me-1"></i>
                        <strong>{{ carrinho.aviso.nome }}</strong> ({{ carrinho.aviso.rotulo }}) adicionado.
                        <div class="mt-2 d-flex flex-wrap gap-2">
                            <a href="carrinho.php" class="btn btn-primary btn-sm">Finalizar compra</a>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="offcanvas"
                                    data-bs-target="#painel-carrinho">Ver carrinho</button>
                        </div>
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" aria-label="Fechar o aviso"
                            @click="carrinho.fecharAviso()"></button>
                </div>
            </div>
        </div>
    `,
};

// Etiquetas de estoque e promoção de uma opção (usadas na vitrine e no produto).
const EtiquetasOpcao = {
    props: { variante: { type: Object, required: true } },
    setup: () => ({ emEstoque, emPromocao, desconto }),
    template: `
        <span v-if="emEstoque(variante)" class="etiqueta etiqueta-normal">
            <i class="bi bi-check-circle-fill"></i>Em estoque
        </span>
        <span v-else class="etiqueta etiqueta-urgente">
            <i class="bi bi-x-circle-fill"></i>Fora de estoque
        </span>
        <span v-if="emPromocao(variante)" class="etiqueta etiqueta-promocao">
            <i class="bi bi-tag-fill"></i>Em promoção · {{ desconto(variante) }}% off
        </span>
    `,
};

// Imagem do produto: foto da opção, ou uma ilustração (a roda muda de cor com o acabamento).
const ImagemOpcao = {
    props: { produto: { type: Object, required: true }, variante: { type: Object, required: true } },
    template: `
        <div class="produto-imagem" :class="{ 'foto-cheia': variante.ajuste === 'preencher' }">
            <img v-if="variante.imagem" :src="variante.imagem" :alt="produto.nome + ' ' + variante.rotulo">
            <svg v-else-if="produto.ilustracao === 'roda'" class="ilustracao" viewBox="0 0 200 200" role="img"
                 :aria-label="produto.nome + ' ' + variante.rotulo">
                <circle cx="100" cy="100" r="96" fill="#1b1e23"/>
                <circle cx="100" cy="100" r="84" fill="#2a2e35"/>
                <circle cx="100" cy="100" r="72" :fill="variante.cor"/>
                <circle cx="100" cy="100" r="62" fill="#101216" opacity="0.55"/>
                <g :fill="variante.cor" stroke="rgba(0,0,0,0.4)" stroke-width="2">
                    <rect v-for="n in 5" :key="n" x="91" y="38" width="18" height="64" rx="9"
                          :transform="'rotate(' + (n * 72) + ' 100 100)'"/>
                </g>
                <circle cx="100" cy="100" r="24" :fill="variante.cor" stroke="rgba(0,0,0,0.4)" stroke-width="2"/>
                <circle v-for="n in 4" :key="'p' + n" cx="100" cy="86" r="3.2" fill="#101216"
                        :transform="'rotate(' + (n * 90) + ' 100 100)'"/>
                <circle cx="100" cy="100" r="7" fill="#101216"/>
            </svg>
            <svg v-else-if="produto.ilustracao === 'oleo'" class="ilustracao" viewBox="0 0 200 200" role="img"
                 :aria-label="produto.nome + ' ' + variante.rotulo">
                <path d="M78 52h44v16c0 6 14 10 14 26v76a12 12 0 0 1-12 12H76a12 12 0 0 1-12-12V94c0-16 14-20 14-26z"
                      fill="#e8a317"/>
                <path d="M72 96v70" stroke="#ffffff" stroke-opacity="0.35" stroke-width="6" stroke-linecap="round"/>
                <rect x="80" y="30" width="40" height="24" rx="5" fill="#14181f"/>
                <rect x="70" y="102" width="60" height="52" rx="6" fill="#ffffff"/>
                <rect x="70" y="102" width="60" height="12" fill="#f26a1b"/>
                <text x="100" y="138" text-anchor="middle" font-size="17" font-weight="800" fill="#14181f"
                      font-family="Inter, system-ui, sans-serif">5W-30</text>
                <text x="100" y="150" text-anchor="middle" font-size="7" font-weight="600" fill="#6b7280"
                      font-family="Inter, system-ui, sans-serif">BRUAGO</text>
            </svg>
            <svg v-else-if="produto.ilustracao === 'palheta'" class="ilustracao" viewBox="0 0 200 200" role="img"
                 :aria-label="produto.nome + ' ' + variante.rotulo">
                <path d="M14 70q86-34 172 0" fill="none" stroke="#9aa3b2" stroke-opacity="0.35" stroke-width="2"
                      stroke-dasharray="4 6"/>
                <path d="M24 150Q100 98 176 150" fill="none" stroke="#14181f" stroke-width="9" stroke-linecap="round"/>
                <path d="M30 144Q100 98 170 144" fill="none" stroke="#6b7280" stroke-width="3" stroke-linecap="round"/>
                <path d="M100 124L62 178" stroke="#4b5563" stroke-width="7" stroke-linecap="round"/>
                <circle cx="62" cy="178" r="9" fill="#14181f"/>
                <circle cx="62" cy="178" r="3.5" fill="#f26a1b"/>
                <g fill="#f26a1b" opacity="0.85">
                    <path d="M150 40c4 7 7 11 7 15a7 7 0 0 1-14 0c0-4 3-8 7-15z"/>
                    <path d="M40 52c3 5 5 8 5 11a5 5 0 0 1-10 0c0-3 2-6 5-11z"/>
                    <path d="M108 28c3 5 5 8 5 11a5 5 0 0 1-10 0c0-3 2-6 5-11z"/>
                </g>
            </svg>
            <i v-else class="bi produto-icone" :class="produto.icone"></i>
        </div>
    `,
};

// Texto com os trechos {destacados} em negrito laranja.
const TextoDestacado = {
    props: { texto: { type: String, required: true } },
    setup: () => ({ partes }),
    template: `
        <template v-for="(parte, i) in partes(texto)" :key="i">
            <strong v-if="parte.destaque" class="texto-destaque">{{ parte.texto }}</strong>
            <template v-else>{{ parte.texto }}</template>
        </template>
    `,
};

const componentesComuns = { PilulaCarrinho, PainelCarrinho, EtiquetasOpcao, ImagemOpcao, TextoDestacado };
