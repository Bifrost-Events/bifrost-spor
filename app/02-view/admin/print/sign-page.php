<?php
/** @var array{route_name: string, stop_title: string, position: int, qr_token: string, scan_url: string, qr_svg: string} $sign */
?>
<section class="sign-card">
    <header class="sign-header">
        <p class="sign-route"><?= htmlspecialchars($sign['route_name'], ENT_QUOTES, 'UTF-8') ?></p>
        <h1 class="sign-title"><?= htmlspecialchars($sign['stop_title'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="sign-position">Post <?= (int) $sign['position'] ?></p>
    </header>

    <div class="sign-qr">
        <?= $sign['qr_svg'] ?>
    </div>

    <p class="sign-instruction">Skann QR-koden for å åpne posten i løypa</p>
    <p class="sign-url"><?= htmlspecialchars($sign['scan_url'], ENT_QUOTES, 'UTF-8') ?></p>
</section>
