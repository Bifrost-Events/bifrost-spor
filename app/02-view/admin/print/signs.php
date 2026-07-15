<?php

use App\Support\AppUrl;
use App\Support\PrintLayout;

/** @var string $title */
/** @var list<array{route_name: string, stop_title: string, position: int, qr_token: string, scan_url: string, qr_svg: string}> $signs */
/** @var int $perPage */
/** @var bool $showLayoutSelector */

$pages = PrintLayout::paginate($signs, $perPage);
$pageCount = PrintLayout::pageCount(count($signs), $perPage);
?>
<!DOCTYPE html>
<html lang="nb">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        :root {
            font-family: system-ui, -apple-system, Segoe UI, sans-serif;
            color: #111827;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: #e5e7eb;
        }

        body.per-page-1 { --sign-title-size: 2rem; --sign-qr-size: 70mm; --sign-instruction-size: 1.15rem; }
        body.per-page-2 { --sign-title-size: 1.45rem; --sign-qr-size: 48mm; --sign-instruction-size: .95rem; }
        body.per-page-4 { --sign-title-size: 1.05rem; --sign-qr-size: 34mm; --sign-instruction-size: .8rem; }

        .no-print {
            max-width: 960px;
            margin: 0 auto;
            padding: 1rem;
        }

        .toolbar {
            background: #fff;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .toolbar-warning {
            margin: 0 0 .75rem;
            padding: .75rem;
            border-radius: 8px;
            background: #fef3c7;
            color: #92400e;
        }

        .toolbar-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
            align-items: center;
        }

        .layout-form {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            align-items: center;
        }

        .layout-form label {
            font-weight: 600;
        }

        .layout-form select {
            padding: .45rem .6rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 1rem;
        }

        .btn {
            display: inline-block;
            background: #0b5fff;
            color: #fff;
            text-decoration: none;
            padding: .6rem 1rem;
            border-radius: 8px;
            border: 0;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-secondary {
            background: #374151;
        }

        .preview-wrap {
            background: #fff;
            padding: 1rem;
        }

        .print-page {
            width: 210mm;
            min-height: 277mm;
            margin: 0 auto 1rem;
            background: #fff;
            border: 1px solid #d1d5db;
            page-break-after: always;
            break-after: page;
        }

        body.per-page-1 .print-page {
            display: block;
        }

        body.per-page-2 .print-page {
            display: grid;
            grid-template-rows: 1fr 1fr;
        }

        body.per-page-4 .print-page {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
        }

        .sign-card {
            padding: 10mm 8mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-height: 0;
        }

        body.per-page-2 .sign-card,
        body.per-page-4 .sign-card {
            border: 1px dashed #e5e7eb;
        }

        .sign-route {
            margin: 0 0 .35rem;
            font-size: .85rem;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        body.per-page-4 .sign-route {
            font-size: .7rem;
        }

        .sign-title {
            margin: 0 0 .25rem;
            font-size: var(--sign-title-size);
            line-height: 1.2;
        }

        .sign-position {
            margin: 0 0 1rem;
            font-size: 1rem;
            color: #6b7280;
        }

        body.per-page-4 .sign-position {
            margin-bottom: .5rem;
            font-size: .85rem;
        }

        .sign-qr svg {
            width: var(--sign-qr-size);
            height: var(--sign-qr-size);
            display: block;
            margin: 0 auto .75rem;
        }

        .sign-instruction {
            margin: 0 0 .5rem;
            font-size: var(--sign-instruction-size);
            font-weight: 600;
        }

        .sign-url {
            margin: 0;
            font-size: .75rem;
            color: #374151;
            word-break: break-all;
        }

        body.per-page-4 .sign-url {
            font-size: .65rem;
        }

        @page {
            size: A4 portrait;
            margin: 12mm;
        }

        @media print {
            body {
                background: #fff;
            }

            .no-print {
                display: none !important;
            }

            .preview-wrap {
                padding: 0;
            }

            .print-page {
                width: auto;
                min-height: auto;
                height: 100vh;
                margin: 0;
                border: 0;
            }

            .sign-card {
                padding: 0;
                border: 0 !important;
            }

            .print-page:last-child {
                page-break-after: auto;
                break-after: auto;
            }
        }
    </style>
</head>
<body class="per-page-<?= (int) $perPage ?>">
    <div class="no-print toolbar">
        <p><strong><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></strong></p>
        <p>
            <?= count($signs) ?> postskilt ·
            <?= $pageCount ?> side<?= $pageCount === 1 ? '' : 'r' ?> (A4, <?= (int) $perPage ?> per side)
        </p>
        <?php
        $appBase = AppUrl::base();
        if (str_contains($appBase, 'localhost') || str_contains($appBase, '127.0.0.1')):
        ?>
            <p class="toolbar-warning">
                QR-kodene peker til <code><?= htmlspecialchars($appBase, ENT_QUOTES, 'UTF-8') ?></code>.
                Mobiltelefoner kan ikke nå localhost — sett <code>APP_URL</code> i <code>.env</code> til en adresse telefonen din kan nå (f.eks. <code>http://spor.bifrost.local/public</code>).
            </p>
        <?php endif; ?>
        <div class="toolbar-actions">
            <?php if ($showLayoutSelector): ?>
                <form class="layout-form" method="get" action="">
                    <label for="per_page">Skilt per side</label>
                    <select id="per_page" name="per_page" onchange="this.form.submit()">
                        <?php foreach (PrintLayout::ALLOWED as $option): ?>
                            <option value="<?= $option ?>"<?= $perPage === $option ? ' selected' : '' ?>>
                                <?= $option ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            <?php endif; ?>
            <button class="btn" type="button" onclick="window.print()">Skriv ut</button>
            <button class="btn btn-secondary" type="button" onclick="history.back()">Tilbake</button>
        </div>
    </div>

    <div class="preview-wrap">
        <?php if ($pages === []): ?>
            <div class="print-page">
                <section class="sign-card">
                    <p>Ingen poster å skrive ut.</p>
                </section>
            </div>
        <?php else: ?>
            <?php foreach ($pages as $pageSigns): ?>
                <div class="print-page">
                    <?php foreach ($pageSigns as $sign): ?>
                        <?php include __DIR__ . '/sign-page.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
