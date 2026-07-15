<?php

declare(strict_types=1);

namespace App\Support;

use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

/**
 * Generates scannable QR codes as inline SVG markup.
 */
final class QrCodeSvgRenderer
{
    public static function render(string $text, int $moduleSize = 6, int $quietZone = 4): string
    {
        $options = new QROptions([
            'outputInterface' => QRMarkupSVG::class,
            'outputBase64' => false,
            'scale' => max(1, $moduleSize),
            'quietzoneSize' => max(0, $quietZone),
            'svgAddXmlHeader' => false,
            'cssClass' => 'spor-qrcode',
            'svgUseFillAttributes' => true,
        ]);

        $svg = (new QRCode($options))->render($text);

        if (!str_contains($svg, 'aria-label')) {
            $svg = preg_replace(
                '/<svg\b/',
                '<svg role="img" aria-label="QR-kode"',
                $svg,
                1
            ) ?? $svg;
        }

        return $svg;
    }
}
