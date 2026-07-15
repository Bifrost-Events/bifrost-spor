<?php ob_start(); ?>
<div class="card">
    <?php if (!empty($error)): ?>
        <p class="flash-error"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <form method="post" action="/registrer">
        <?= $csrf ?? '' ?>
        <input type="hidden" name="redirect" value="<?= htmlspecialchars((string) ($redirect ?? '/'), ENT_QUOTES, 'UTF-8') ?>">
        <label for="name">Navn</label>
        <input id="name" name="name" value="<?= htmlspecialchars((string) ($old['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>

        <label for="email">E-post</label>
        <input id="email" name="email" type="email" value="<?= htmlspecialchars((string) ($old['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>

        <label for="password">Passord (minst 8 tegn)</label>
        <input id="password" name="password" type="password" minlength="8" required>

        <label for="password_confirm">Bekreft passord</label>
        <input id="password_confirm" name="password_confirm" type="password" minlength="8" required>

        <p style="margin-top: 1rem;">
            <button class="btn" type="submit">Registrer</button>
            <a href="/logg-inn">Har du konto? Logg inn</a>
        </p>
    </form>
</div>
<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/public/layout.php';
