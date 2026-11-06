<?php

declare(strict_types=1);

use Mecanica\Web\Html;
use Mecanica\Web\Listagem;

/** @var Listagem $listagem lista já com o total informado */
$ultima = $listagem->totalPaginas();
if ($ultima <= 1) {
    return;
}
$atual = $listagem->pagina;
$inicio = max(1, min($atual - 2, $ultima - 4));
$fim = min($ultima, $inicio + 4);
?>
<nav aria-label="Páginas">
    <ul class="pagination pagination-sm paginacao">
        <li class="page-item <?= $atual === 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= Html::e($listagem->url(['pagina' => $atual - 1])) ?>" aria-label="Anterior">
                <i class="bi bi-chevron-left"></i>
            </a>
        </li>
        <?php for ($p = $inicio; $p <= $fim; $p++): ?>
            <li class="page-item <?= $p === $atual ? 'active' : '' ?>">
                <a class="page-link" href="<?= Html::e($listagem->url(['pagina' => $p])) ?>"
                   <?= $p === $atual ? 'aria-current="page"' : '' ?>><?= $p ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= $atual === $ultima ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= Html::e($listagem->url(['pagina' => $atual + 1])) ?>" aria-label="Próxima">
                <i class="bi bi-chevron-right"></i>
            </a>
        </li>
    </ul>
</nav>
