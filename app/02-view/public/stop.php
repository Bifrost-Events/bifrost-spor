<?php
/** @var \App\Domain\Route $route */
/** @var \App\Domain\Stop $stop */
/** @var \App\Domain\Stop|null $prev */
/** @var \App\Domain\Stop|null $next */
/** @var bool $viaQr */
/** @var array{id: string, name: string, email: string}|null $participant */
/** @var \App\Domain\Answer|null $existingAnswer */
/** @var array{selected_option_id: string, is_correct: bool}|null $guestAnswer */

$stopPath = '/spor/' . rawurlencode($route->slug) . '/' . rawurlencode($stop->slug);
$stopUrl = $viaQr ? $stopPath . '?qr=1' : $stopPath;
$loginUrl = '/logg-inn?redirect=' . rawurlencode($stopUrl);
$registerUrl = '/registrer?redirect=' . rawurlencode($stopUrl);

ob_start();
?>
<p class="muted">
    <a href="/spor/<?= htmlspecialchars($route->slug, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($route->name, ENT_QUOTES, 'UTF-8') ?></a>
    · Post <?= (int) $stop->position ?>
    <?php if (!$viaQr): ?>
        · <a href="/spor/<?= htmlspecialchars($route->slug, ENT_QUOTES, 'UTF-8') ?>/resultater">Resultattavle</a>
    <?php endif; ?>
</p>

<?php if ($participant === null): ?>
    <div class="flash-info">
        Du kan lese og svare på denne posten uten å være innlogget.
        <a href="<?= htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') ?>">Logg inn</a>
        eller
        <a href="<?= htmlspecialchars($registerUrl, ENT_QUOTES, 'UTF-8') ?>">registrer deg</a>
        for å få svaret ditt registrert i konkurransen.
    </div>
<?php endif; ?>

<article class="card">
    <h2><?= htmlspecialchars($stop->title, ENT_QUOTES, 'UTF-8') ?></h2>
    <div><?= nl2br(htmlspecialchars($stop->body, ENT_QUOTES, 'UTF-8')) ?></div>

    <?php if ($stop->question !== null): ?>
        <div class="question">
            <strong>Konkurransespørsmål</strong>
            <p><?= nl2br(htmlspecialchars($stop->question->text, ENT_QUOTES, 'UTF-8')) ?></p>

            <?php if ($existingAnswer !== null): ?>
                <div class="answer-result <?= $existingAnswer->isCorrect ? 'correct' : 'incorrect' ?>">
                    <?php if ($existingAnswer->isCorrect): ?>
                        Du svarte riktig. Svaret er registrert.
                    <?php else: ?>
                        Du svarte feil. Svaret er registrert.
                    <?php endif; ?>
                </div>
            <?php elseif ($guestAnswer !== null): ?>
                <div class="answer-result <?= $guestAnswer['is_correct'] ? 'correct' : 'incorrect' ?>">
                    <?php if ($guestAnswer['is_correct']): ?>
                        Du svarte riktig.
                    <?php else: ?>
                        Du svarte feil denne gangen.
                    <?php endif; ?>
                </div>
                <p class="muted" style="margin-top: .75rem;">
                    <a href="<?= htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') ?>">Logg inn</a>
                    for å registrere svaret på resultattavlen.
                </p>
            <?php else: ?>
                <form method="post" action="/spor/<?= htmlspecialchars($route->slug, ENT_QUOTES, 'UTF-8') ?>/<?= htmlspecialchars($stop->slug, ENT_QUOTES, 'UTF-8') ?>/svar">
                    <?= $csrf ?? '' ?>
                    <?php if ($viaQr): ?>
                        <input type="hidden" name="via_qr" value="1">
                    <?php endif; ?>
                    <?php foreach ($stop->question->options as $option): ?>
                        <label class="option">
                            <input type="radio" name="option_id" value="<?= htmlspecialchars($option->id, ENT_QUOTES, 'UTF-8') ?>" required>
                            <?= htmlspecialchars($option->text, ENT_QUOTES, 'UTF-8') ?>
                        </label>
                    <?php endforeach; ?>
                    <?php if ($participant === null): ?>
                        <p class="muted" style="margin-top: .75rem;">Svaret lagres i nettleseren til du logger inn.</p>
                    <?php endif; ?>
                    <p style="margin-top: 1rem;">
                        <button class="btn" type="submit">Send svar</button>
                    </p>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</article>
<?php if (!$viaQr && ($prev !== null || $next !== null)): ?>
<nav>
    <?php if ($prev !== null): ?>
        <a href="/spor/<?= htmlspecialchars($route->slug, ENT_QUOTES, 'UTF-8') ?>/<?= htmlspecialchars($prev->slug, ENT_QUOTES, 'UTF-8') ?>">← Forrige</a>
    <?php endif; ?>
    <?php if ($next !== null): ?>
        <a href="/spor/<?= htmlspecialchars($route->slug, ENT_QUOTES, 'UTF-8') ?>/<?= htmlspecialchars($next->slug, ENT_QUOTES, 'UTF-8') ?>">Neste →</a>
    <?php endif; ?>
</nav>
<?php endif; ?>
<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/public/layout.php';
