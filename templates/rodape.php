<?php

declare(strict_types=1);

/** @var list<string> $scripts scripts extras da página (opcional) */
?>
</main>
<footer class="rodape">Mecânica Bruago &middot; Projeto de Web 2 do curso técnico da QI</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= $raiz ?>js/confirmar.js"></script>
<script src="<?= $raiz ?>js/mascaras.js"></script>
<?php foreach ($scripts ?? [] as $script): ?>
    <script src="<?= $script ?>"></script>
<?php endforeach; ?>
</body>
</html>
