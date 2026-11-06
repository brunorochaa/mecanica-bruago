# CLAUDE.md

> **Sincronia obrigatória:** `CLAUDE.md` e `AGENTS.md` devem ter o mesmo conteúdo (só o título muda). Sempre que um for alterado, atualize o outro.

## O que é

Sistema web de uma oficina mecânica (clientes, serviços e vitrine de produtos), projeto final de Web 2 do curso técnico da QI (Bruno Rocha e Thiago Dias), modernizado em 2026. Uso e instalação no `README.md`.

## Stack

PHP 8.2+ puro (sem framework, testado no 8.5), PDO com MySQL/MariaDB, Bootstrap 5.3 e Vue 3.5 pela CDN (versões fixas). Sem Composer obrigatório: o autoload está em `src/bootstrap.php`.

## Como rodar / testar

```bash
php -S localhost:8000 -t public   # precisa de config/config.php (copie de config.example.php)
php tests/executar.php            # SQLite em memória; precisa das extensões pdo_sqlite e mbstring
```

## Estrutura

- `public/`: única pasta servida. Cada página faz `require` de `src/bootstrap.php`, trata o POST no topo (validar → salvar → `Pagina::redirecionar`) e só depois inclui `templates/cabecalho.php` e o HTML. Cada módulo tem `index.php` (lista + pesquisa por GET), `formulario.php` (novo sem `id`, edição com `?id=`) e `excluir.php` (só POST).
- `src/` (namespace `Mecanica\`): `Banco/Conexao`, `Modelo/` (classes com propriedades tipadas e enums com `rotulo()`), `Repositorio/` (SQL; `listar()` aceita ordem, limite e deslocamento, `contar()` serve a paginação), `Validacao/` (`*Validador::validar($_POST, $id)` devolve `Resultado` com valores normalizados, erros por campo e o objeto), `Web/Html` (escape e formatação), `Web/Listagem` (pesquisa, ordem e página na URL, só com valores permitidos) e `Web/Pagina` (redirecionar, avisos flash, CSRF).
- `templates/`: `cabecalho.php` (usa `$titulo`, `$raiz`, `$menuAtivo`; opcionais `$subtitulo`, `$acao`, `$semTitulo`), `rodape.php` (aceita `$scripts`) e `paginacao.php` (usa `$listagem`). Tema claro/escuro por `data-bs-theme` (`public/js/tema.js`); as cores ficam em variáveis no `public/css/estilo.css`.
- `banco/mecanica.sql`: schema MySQL com dados fictícios; `tests/schema-sqlite.sql` é o mesmo schema para os testes. Mudou um, mude o outro.

## Convenções

- PHP: `declare(strict_types=1)` em todo arquivo, PSR-12 (4 espaços), classes em `PascalCase` (um arquivo por classe), métodos e variáveis em `camelCase`, nomes e textos em pt-BR. Páginas e pastas públicas em minúsculas (`kebab-case` se tiver mais de uma palavra).
- As colunas do banco continuam em `camelCase` (`idCliente`, `dataNascimento`, `tipoCombustivel`), como no projeto original; os valores dos enums são os mesmos gravados no banco (`assisBrasil`, `comUrgencia`...).
- SQL só com parâmetros (`?`). Coluna dinâmica só pelas listas `CAMPOS_PESQUISA` (pesquisa) e `ORDENAVEIS` (ordenação) do repositório.
- Guardar o texto como digitado (normalizado) e escapar na saída com `Html::e()`; nunca `htmlentities` antes de gravar.
- Formulários que alteram dados: POST + `Pagina::campoCsrf()` / `Pagina::exigirCsrf()`. Confirmação com o atributo `data-confirmar` (script `public/js/confirmar.js`), sem JavaScript inline.
- Não commitar `config/config.php`, `node_modules/` nem dados pessoais reais no SQL.
- Formatação: `.editorconfig` (4 espaços, LF, UTF-8).

## README com prints

- O `README.md` mostra o projeto com prints em `docs/imagens/` (PNG; 1280 de largura no computador, 500 no celular; poucos, só as telas principais), em tabelas lado a lado.
- Só dados fictícios nas imagens: nada de nome, telefone, e-mail ou chave reais, e nada do serviço na tela.
- Refaça os prints só quando a tela mudar de verdade: gere de novo e compare o tamanho do arquivo; se for idêntico, mantenha o antigo (evita inchar o repositório).
- Como tirar: Chrome sem janela, `chrome.exe --headless=new --hide-scrollbars --window-size=1280,900 --virtual-time-budget=10000 --screenshot=<arquivo> <url>`, com um `--user-data-dir=<pasta temporária>` para cada print. Site estático abre por `file:///`; PHP sobe com `C:\Users\bruno\php\php.exe -S localhost:8000 -t public` (o PHP não está no PATH). O Chrome sem janela não passa de uns 500 de largura.
- A rotina completa está na skill `/readme` do Claude Code.
- Neste projeto: banco de demonstração em SQLite (o `config/config.php` local aponta para ele e não é versionado); `?tema=claro` ou `?tema=escuro` força o tema; o carrinho vem do `localStorage` (`carrinho-bruago`, lista de ids das opções), então preencha-o antes de tirar o print.

## Notas para futuros agentes

- Reescrito em 2026-11-06 a pedido do Bruno ("atualizar pro PHP novo"). Problemas da versão antiga, já corrigidos: SQL injection nas buscas, exclusão por link GET (o registro sumia só no próximo carregamento), edição sem validação, filtro `dataNascimendo` digitado errado, filial Santa Rosa nunca marcada na edição, Bootstrap/jQuery carregados de `node_modules` que não estava no repositório, Vue sem versão (a CDN passou a servir o Vue 3 e a página quebrou), dados pessoais reais no `mecanica.sql`.
- O histórico do Git ainda contém o `mecanica.sql` antigo com dados pessoais; limpar exige reescrever o histórico (decisão do Bruno).
- Estado em 2026-11-06: redesign, loja e carrinho prontos e publicados no GitHub; site em hospedagem gratuita (InfinityFree) sem login, então as telas de admin estão abertas. Próximo passo combinado com o Bruno: login só de administrador, depois cadastro aberto de clientes e pedidos no banco; uma versão em Laravel fica para outra branch/repositório (por isso esta continua sem framework). Detalhes em "Próximos passos" do `README.md`.
- Após alterações: `php -l` nos arquivos alterados e `php tests/executar.php`.

## Regras gerais

- Use português do Brasil (pt-BR) nas respostas ao usuário, na documentação, nas mensagens das telas e nos textos novos do código.
- Prefira soluções simples e fáceis de manter; não adicione framework ou dependência sem necessidade.
- Não altere arquivos ou funcionalidades fora do escopo da tarefa sem explicar o motivo.
- Atualize o `README.md` quando mudar instalação, estrutura ou comportamento importante.
