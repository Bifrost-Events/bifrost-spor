<?php

declare(strict_types=1);

namespace Tests;

use App\Support\QrCodeSvgRenderer;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

final class QrCodeSvgRendererTest extends SporTestCase
{
    public function testRenderProducesSvgMarkup(): void
    {
        $url = 'https://spor.example.test/q/abc123token';

        $svg = QrCodeSvgRenderer::render($url);

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('</svg>', $svg);
        $this->assertStringContainsString('aria-label="QR-kode"', $svg);
    }

    public function testRenderedQrCodeCanBeDecodedWhenGdIsAvailable(): void
    {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('GD extension is not available.');
        }

        $url = 'https://spor.example.test/q/decode-me-token';
        $options = new QROptions([
            'outputInterface' => QRGdImagePNG::class,
            'outputBase64' => false,
            'scale' => 8,
        ]);

        $png = (new QRCode($options))->render($url);
        $decoded = (new QRCode())->readFromBlob($png);

        $this->assertSame($url, $decoded->data);
    }
}
