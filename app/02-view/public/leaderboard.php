<?php
/** @var \App\Domain\Route $route */
/** @var list<array{participant_id: string, name: string, correct: int, total_answered: int, total_questions: int, score_percent: int, last_answered_at: ?string}> $entries */
ob_start();
?>
<p class="muted">
    <a href="/spor/<?= htmlspecialchars($route->slug, ENT_QUOTES, 'UTF-8') ?>">← Tilbake til <?= htmlspecialchars($route->name, ENT_QUOTES, 'UTF-8') ?></a>
</p>
<div class="card">
    <?php if ($entries === []): ?>
        <p class="muted">Ingen har svart ennå. Vær den første!</p>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>#</th>
                <th>Navn</th>
                <th>Riktige</th>
                <th>Besvart</th>
                <th>Poeng</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($entries as $index => $entry): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($entry['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int) $entry['correct'] ?> / <?= (int) $entry['total_questions'] ?></td>
                    <td><?= (int) $entry['total_answered'] ?></td>
                    <td><?= (int) $entry['score_percent'] ?>%</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/public/layout.php';
