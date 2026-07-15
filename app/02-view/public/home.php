<?php
/** @var \App\Domain\Route[] $routes */
ob_start();
?>
<?php if ($routes === []): ?>
    <p class="muted">Ingen publiserte løyper ennå.</p>
<?php else: ?>
    <?php foreach ($routes as $route): ?>
        <article class="card">
            <h2><a href="/spor/<?= htmlspecialchars($route->slug, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($route->name, ENT_QUOTES, 'UTF-8') ?></a></h2>
            <?php if ($route->description !== ''): ?>
                <p><?= nl2br(htmlspecialchars($route->description, ENT_QUOTES, 'UTF-8')) ?></p>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
<?php endif; ?>
<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/public/layout.php';
