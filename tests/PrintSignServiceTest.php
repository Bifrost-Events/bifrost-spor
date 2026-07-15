<?php

declare(strict_types=1);

namespace Tests;

use App\Service\PrintSignService;
use App\Support\AppUrl;

final class PrintSignServiceTest extends SporTestCase
{
    public function testBuildSignUsesQrScanUrl(): void
    {
        $_ENV['APP_URL'] = 'https://spor.example.test/public';

        $route = $this->container->routeService()->create([
            'owner_id' => 'participant_test',
            'name' => 'Testløype',
            'slug' => 'testloype',
            'description' => '',
            'status' => \App\Domain\Route::STATUS_PUBLISHED,
            'theme' => 'default',
        ]);

        $stop = $this->container->stopService()->create($route->id, [
            'title' => 'Velkommen',
            'slug' => 'velkommen',
            'body' => 'Tekst',
            'position' => '1',
            'status' => \App\Domain\Stop::STATUS_PUBLISHED,
        ]);

        $sign = (new PrintSignService())->buildSign($route, $stop);

        $this->assertSame('Testløype', $sign['route_name']);
        $this->assertSame('Velkommen', $sign['stop_title']);
        $this->assertSame(
            AppUrl::qrScanUrl($stop->qrToken),
            $sign['scan_url']
        );
        $this->assertStringContainsString('<svg', $sign['qr_svg']);
        $this->assertStringContainsString('</svg>', $sign['qr_svg']);
    }
}
