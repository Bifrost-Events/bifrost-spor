<?php
/** @var \App\Domain\Route $route */
/** @var array<string, mixed> $old */
ob_start();
?>
<div class="card">
    <p class="muted">Løype: <?= htmlspecialchars($route->name, ENT_QUOTES, 'UTF-8') ?></p>
    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <form method="post" action="/admin/routes/<?= rawurlencode($route->id) ?>/stops">
        <?= $csrf ?? '' ?>
        <label for="title">Tittel</label>
        <input id="title" name="title" value="<?= htmlspecialchars((string) ($old['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>

        <label for="slug">Slug</label>
        <input id="slug" name="slug" value="<?= htmlspecialchars((string) ($old['slug'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Genereres fra tittel hvis tom">

        <label for="body">Innhold</label>
        <textarea id="body" name="body" rows="6"><?= htmlspecialchars((string) ($old['body'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>

        <h3 style="margin-top: 1.25rem;">Konkurransespørsmål</h3>
        <label for="question_text">Spørsmål</label>
        <textarea id="question_text" name="question_text" rows="3"><?= htmlspecialchars((string) ($old['question_text'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>

        <label for="option_1">Alternativ 1</label>
        <input id="option_1" name="option_1" value="<?= htmlspecialchars((string) ($old['option_1'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

        <label for="option_2">Alternativ 2</label>
        <input id="option_2" name="option_2" value="<?= htmlspecialchars((string) ($old['option_2'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

        <label for="option_3">Alternativ 3</label>
        <input id="option_3" name="option_3" value="<?= htmlspecialchars((string) ($old['option_3'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

        <label for="option_4">Alternativ 4</label>
        <input id="option_4" name="option_4" value="<?= htmlspecialchars((string) ($old['option_4'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

        <label for="correct_option">Riktig alternativ</label>
        <select id="correct_option" name="correct_option">
            <?php for ($i = 1; $i <= 4; $i++): ?>
                <option value="<?= $i ?>" <?= ((string) ($old['correct_option'] ?? '1') === (string) $i) ? 'selected' : '' ?>>Alternativ <?= $i ?></option>
            <?php endfor; ?>
        </select>

        <label for="position">Posisjon</label>
        <input id="position" name="position" type="number" min="1" value="<?= htmlspecialchars((string) ($old['position'] ?? '1'), ENT_QUOTES, 'UTF-8') ?>" required>

        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="draft" <?= (($old['status'] ?? 'draft') === 'draft') ? 'selected' : '' ?>>Utkast</option>
            <option value="published" <?= (($old['status'] ?? '') === 'published') ? 'selected' : '' ?>>Publisert</option>
        </select>

        <p style="margin-top: 1rem;">
            <button class="btn" type="submit">Lagre post</button>
            <a href="/admin/routes/<?= rawurlencode($route->id) ?>">Avbryt</a>
        </p>
    </form>
</div>
<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/../../layout.php';
