<?php
/** @var \App\Domain\Route[] $routes */
/** @var array<string, int> $stopCounts */
ob_start();
?>
<div style="margin-bottom: 1rem;">
    <a class="btn" href="/admin/routes/create">Opprett løype</a>
</div>
<div class="card">
    <?php if ($routes === []): ?>
        <p class="muted">Ingen løyper ennå.</p>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>Navn</th>
                <th>Status</th>
                <th>Poster</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($routes as $route): ?>
                <tr>
                    <td><?= htmlspecialchars($route->name, ENT_QUOTES, 'UTF-8') ?></td>
                    <td><span class="badge"><?= htmlspecialchars($route->status, ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td><?= (int) ($stopCounts[$route->id] ?? 0) ?></td>
                    <td><a href="/admin/routes/<?= rawurlencode($route->id) ?>">Detaljer</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layout.php';
