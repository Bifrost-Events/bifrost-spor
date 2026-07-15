<!DOCTYPE html>
<html lang="nb">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars((string) ($title ?? 'Admininnlogging'), ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f3f4f6; margin: 0; }
        .wrap { max-width: 420px; margin: 4rem auto; padding: 1rem; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 1.25rem; }
        label { display: block; margin-top: .75rem; font-weight: 600; }
        input { width: 100%; box-sizing: border-box; padding: .55rem; margin-top: .25rem; }
        button { margin-top: 1rem; width: 100%; padding: .65rem; background: #0b5fff; color: #fff; border: 0; border-radius: 8px; }
        .error { background: #fef2f2; color: #991b1b; padding: .75rem; border-radius: 8px; margin-bottom: 1rem; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1>Admininnlogging</h1>
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <form method="post" action="/admin/login">
            <?= $csrf ?? '' ?>
            <label for="username">Brukernavn</label>
            <input id="username" name="username" autocomplete="username" required>
            <label for="password">Passord</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
            <button type="submit">Logg inn</button>
        </form>
    </div>
</div>
</body>
</html>
