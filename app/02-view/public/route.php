<?php
/** @var \App\Domain\Route $route */
/** @var \App\Domain\Stop[] $stops */
ob_start();
?>
<p class="muted"><?= nl2br(htmlspecialchars($route->description, ENT_QUOTES, 'UTF-8')) ?></p>
<p><a class="btn" href="/spor/<?= htmlspecialchars($route->slug, ENT_QUOTES, 'UTF-8') ?>/resultater">Se resultattavle</a></p>

<?php if ($stops === []): ?>
    <p class="muted">Ingen publiserte poster i denne løypa ennå.</p>
<?php else: ?>
    <ol>
        <?php foreach ($stops as $stop): ?>
            <li>
                <a href="/spor/<?= htmlspecialchars($route->slug, ENT_QUOTES, 'UTF-8') ?>/<?= htmlspecialchars($stop->slug, ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($stop->title, ENT_QUOTES, 'UTF-8') ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ol>
<?php endif; ?>
<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/public/layout.php';
