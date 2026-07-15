<!DOCTYPE html>
<html lang="nb">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars((string) ($title ?? 'Mine løyper'), ENT_QUOTES, 'UTF-8') ?> · Bifrost Spor</title>
    <style>
        :root { font-family: system-ui, -apple-system, Segoe UI, sans-serif; line-height: 1.5; }
        body { margin: 0; background: #f3f4f6; color: #111827; }
        .wrap { max-width: 960px; margin: 0 auto; padding: 1rem; }
        header { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap; }
        a { color: #0b5fff; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 1rem; margin-bottom: 1rem; }
        .btn { display: inline-block; background: #0b5fff; color: #fff; text-decoration: none; padding: .55rem .9rem; border-radius: 8px; border: 0; cursor: pointer; }
        .btn-secondary { background: #374151; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: .6rem; border-bottom: 1px solid #e5e7eb; }
        label { display: block; margin-top: .75rem; font-weight: 600; }
        input, textarea, select { width: 100%; box-sizing: border-box; padding: .55rem; margin-top: .25rem; border: 1px solid #d1d5db; border-radius: 8px; }
        .error { background: #fef2f2; color: #991b1b; padding: .75rem; border-radius: 8px; margin-bottom: 1rem; }
        .flash-success, .flash-error, .flash-info { padding: .75rem; border-radius: 8px; margin-bottom: 1rem; }
        .flash-success { background: #ecfdf5; color: #065f46; }
        .flash-error { background: #fef2f2; color: #991b1b; }
        .flash-info { background: #eff6ff; color: #1e40af; }
        .badge { display: inline-block; padding: .15rem .5rem; border-radius: 999px; font-size: .8rem; background: #eef2ff; }
        .muted { color: #6b7280; }
    </style>
</head>
<body>
<?php
use App\Support\Session;
$navParticipant = $participant ?? Session::getParticipant();
?>
<div class="wrap">
    <header>
        <div>
            <strong>Bifrost Spor</strong> · Mine løyper
        </div>
        <nav>
            <a href="/">Forside</a>
            <a href="/admin/routes">Mine løyper</a>
            <?php if ($navParticipant !== null): ?>
                <span class="muted"><?= htmlspecialchars((string) $navParticipant['name'], ENT_QUOTES, 'UTF-8') ?></span>
                <a href="/logg-ut">Logg ut</a>
            <?php else: ?>
                <a href="/logg-inn?redirect=<?= rawurlencode('/admin/routes') ?>">Logg inn</a>
            <?php endif; ?>
        </nav>
    </header>
    <?php include dirname(__DIR__) . '/partials/flash.php'; ?>
    <main>
        <?= $content ?? '' ?>
    </main>
</div>
</body>
</html>
