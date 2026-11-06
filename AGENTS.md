# AGENTS.md

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

## Notas para futuros agentes

- Reescrito em 2026-11-06 a pedido do Bruno ("atualizar pro PHP novo"). Problemas da versão antiga, já corrigidos: SQL injection nas buscas, exclusão por link GET (o registro sumia só no próximo carregamento), edição sem validação, filtro `dataNascimendo` digitado errado, filial Santa Rosa nunca marcada na edição, Bootstrap/jQuery carregados de `node_modules` que não estava no repositório, Vue sem versão (a CDN passou a servir o Vue 3 e a página quebrou), dados pessoais reais no `mecanica.sql`.
- O histórico do Git ainda contém o `mecanica.sql` antigo com dados pessoais; limpar exige reescrever o histórico (decisão do Bruno).
- Após alterações: `php -l` nos arquivos alterados e `php tests/executar.php`.

## Regras gerais

- Use português do Brasil (pt-BR) nas respostas ao usuário, na documentação, nas mensagens das telas e nos textos novos do código.
- Prefira soluções simples e fáceis de manter; não adicione framework ou dependência sem necessidade.
- Não altere arquivos ou funcionalidades fora do escopo da tarefa sem explicar o motivo.
- Atualize o `README.md` quando mudar instalação, estrutura ou comportamento importante.
