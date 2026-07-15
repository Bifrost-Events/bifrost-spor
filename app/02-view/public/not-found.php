<?php ob_start(); ?>
<p>Siden du leter etter finnes ikke.</p>
<p><a href="/">Til forsiden</a></p>
<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/public/layout.php';
