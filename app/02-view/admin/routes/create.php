<?php
/** @var string[] $themes */
/** @var array<string, mixed> $old */
ob_start();
?>
<div class="card">
    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <form method="post" action="/admin/routes">
        <?= $csrf ?? '' ?>
        <label for="name">Navn</label>
        <input id="name" name="name" value="<?= htmlspecialchars((string) ($old['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>

        <label for="slug">Slug</label>
        <input id="slug" name="slug" value="<?= htmlspecialchars((string) ($old['slug'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Genereres fra navn hvis tom">

        <label for="description">Beskrivelse</label>
        <textarea id="description" name="description" rows="4"><?= htmlspecialchars((string) ($old['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>

        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="draft" <?= (($old['status'] ?? 'draft') === 'draft') ? 'selected' : '' ?>>Utkast</option>
            <option value="published" <?= (($old['status'] ?? '') === 'published') ? 'selected' : '' ?>>Publisert</option>
        </select>

        <label for="theme">Tema</label>
        <select id="theme" name="theme">
            <?php foreach ($themes as $theme): ?>
                <option value="<?= htmlspecialchars((string) $theme, ENT_QUOTES, 'UTF-8') ?>" <?= (($old['theme'] ?? 'default') === $theme) ? 'selected' : '' ?>>
                    <?= htmlspecialchars((string) $theme, ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>

        <p style="margin-top: 1rem;">
            <button class="btn" type="submit">Lagre løype</button>
            <a href="/admin/routes">Avbryt</a>
        </p>
    </form>
</div>
<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layout.php';
