<?php ob_start(); ?>
<div class="card">
    <?php if (!empty($error)): ?>
        <p class="flash-error"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <form method="post" action="/logg-inn">
        <?= $csrf ?? '' ?>
        <input type="hidden" name="redirect" value="<?= htmlspecialchars((string) ($redirect ?? '/'), ENT_QUOTES, 'UTF-8') ?>">
        <label for="email">E-post</label>
        <input id="email" name="email" type="email" autocomplete="email" required>

        <label for="password">Passord</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>

        <p style="margin-top: 1rem;">
            <button class="btn" type="submit">Logg inn</button>
            <a href="/registrer">Ny bruker? Registrer deg</a>
        </p>
    </form>
</div>
<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/public/layout.php';
