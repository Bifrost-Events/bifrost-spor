<!DOCTYPE html>
<html lang="nb">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars((string) ($title ?? 'Bifrost Spor'), ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        :root { color-scheme: light; font-family: system-ui, -apple-system, Segoe UI, sans-serif; line-height: 1.5; }
        body { margin: 0; background: #f6f7f9; color: #1f2937; }
        .wrap { max-width: 720px; margin: 0 auto; padding: 1rem; }
        header { margin-bottom: 1rem; }
        a { color: #0b5fff; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 1rem; margin-bottom: 1rem; }
        .muted { color: #6b7280; font-size: .95rem; }
        .badge { display: inline-block; padding: .15rem .5rem; border-radius: 999px; font-size: .8rem; background: #eef2ff; }
        nav a { margin-right: .75rem; }
        .question { margin-top: 1.25rem; padding: 1rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; }
        .question strong { display: block; margin-bottom: .35rem; color: #166534; }
        .question p { margin: 0 0 .75rem; }
        .option { display: block; margin: .5rem 0; padding: .65rem .75rem; background: #fff; border: 1px solid #d1d5db; border-radius: 8px; cursor: pointer; }
        .option input { margin-right: .5rem; }
        .btn { display: inline-block; background: #0b5fff; color: #fff; text-decoration: none; padding: .55rem .9rem; border-radius: 8px; border: 0; cursor: pointer; }
        .flash-success, .flash-error, .flash-info { padding: .75rem; border-radius: 8px; margin-bottom: 1rem; }
        .flash-success { background: #ecfdf5; color: #065f46; }
        .flash-error { background: #fef2f2; color: #991b1b; }
        .flash-info { background: #eff6ff; color: #1e40af; }
        .answer-result { margin-top: .75rem; padding: .75rem; border-radius: 8px; }
        .answer-result.correct { background: #ecfdf5; color: #065f46; }
        .answer-result.incorrect { background: #fef2f2; color: #991b1b; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: .6rem; border-bottom: 1px solid #e5e7eb; }
        label { display: block; margin-top: .75rem; font-weight: 600; }
        input { width: 100%; box-sizing: border-box; padding: .55rem; margin-top: .25rem; border: 1px solid #d1d5db; border-radius: 8px; }
    </style>
</head>
<body>
<div class="wrap">
    <header>
        <nav>
            <a href="/">Forside</a>
            <?php if (!empty($participant)): ?>
                <span class="muted">Hei, <?= htmlspecialchars((string) $participant['name'], ENT_QUOTES, 'UTF-8') ?></span>
                <a href="/admin/routes">Mine løyper</a>
                <a href="/logg-ut">Logg ut</a>
            <?php else: ?>
                <a href="/logg-inn">Logg inn</a>
                <a href="/registrer">Registrer</a>
            <?php endif; ?>
        </nav>
        <h1><?= htmlspecialchars((string) ($title ?? ''), ENT_QUOTES, 'UTF-8') ?></h1>
    </header>
    <?php if (!empty($flash)): ?>
        <div class="flash-<?= htmlspecialchars((string) ($flash['type'] ?? 'info'), ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars((string) ($flash['message'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>
    <main>
        <?= $content ?? '' ?>
    </main>
</div>
</body>
</html>
