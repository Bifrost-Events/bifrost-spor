<?php
/** @var \App\Domain\Route $route */
/** @var \App\Domain\Stop[] $stops */
ob_start();
?>
<div style="margin-bottom: 1rem;">
    <a class="btn" href="/admin/routes/<?= rawurlencode($route->id) ?>/stops/create">Opprett post</a>
    <?php if ($stops !== []): ?>
        <a class="btn btn-secondary" href="/admin/routes/<?= rawurlencode($route->id) ?>/print">Skriv ut alle postskilt</a>
    <?php endif; ?>
    <?php if ($route->status === 'published'): ?>
        <a class="btn btn-secondary" href="/spor/<?= htmlspecialchars($route->slug, ENT_QUOTES, 'UTF-8') ?>">Offentlig side</a>
    <?php endif; ?>
</div>

<div class="card">
    <h2>Grunninformasjon</h2>
    <p><strong>Navn:</strong> <?= htmlspecialchars($route->name, ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>Slug:</strong> <?= htmlspecialchars($route->slug, ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>Status:</strong> <span class="badge"><?= htmlspecialchars($route->status, ENT_QUOTES, 'UTF-8') ?></span></p>
    <p><strong>Tema:</strong> <?= htmlspecialchars($route->theme, ENT_QUOTES, 'UTF-8') ?></p>
    <?php if ($route->description !== ''): ?>
        <p><?= nl2br(htmlspecialchars($route->description, ENT_QUOTES, 'UTF-8')) ?></p>
    <?php endif; ?>
</div>

<div class="card">
    <h2>Poster</h2>
    <?php if ($stops === []): ?>
        <p class="muted">Ingen poster ennå.</p>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>#</th>
                <th>Tittel</th>
                <th>Status</th>
                <th>QR-token</th>
                <th>Offentlig URL</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($stops as $stop): ?>
                <tr>
                    <td><?= (int) $stop->position ?></td>
                    <td><?= htmlspecialchars($stop->title, ENT_QUOTES, 'UTF-8') ?></td>
                    <td><span class="badge"><?= htmlspecialchars($stop->status, ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td><code><?= htmlspecialchars($stop->qrToken, ENT_QUOTES, 'UTF-8') ?></code></td>
                    <td><a href="/q/<?= rawurlencode($stop->qrToken) ?>">/q/<?= htmlspecialchars($stop->qrToken, ENT_QUOTES, 'UTF-8') ?></a></td>
                    <td><a href="/admin/stops/<?= rawurlencode($stop->id) ?>/print">Skriv ut post</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layout.php';
