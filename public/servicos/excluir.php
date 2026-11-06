<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/src/bootstrap.php';

use Mecanica\Banco\Conexao;
use Mecanica\Repositorio\ServicoRepositorio;
use Mecanica\Web\Pagina;

// Exclusão só por POST (formulário com confirmação na listagem), nunca por link.
if (!Pagina::ehPost()) {
    Pagina::redirecionar('index.php');
}
Pagina::exigirCsrf();

$id = Pagina::id($_POST);
if ($id !== null) {
    (new ServicoRepositorio(Conexao::obter()))->excluir($id);
    Pagina::avisar('Serviço excluído.');
}
Pagina::redirecionar('index.php');
