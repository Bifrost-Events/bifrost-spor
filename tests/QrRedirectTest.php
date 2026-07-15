<?php

declare(strict_types=1);

namespace Tests;

use App\Controller\QrRedirectController;
use App\Domain\Route;
use App\Domain\Stop;
use App\Support\Session;

final class QrRedirectTest extends SporTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Session::clearParticipant();
    }

    public function testGuestCanOpenPublishedStopDirectlyFromQrToken(): void
    {
        $route = $this->container->routeService()->create([
            'owner_id' => 'participant_test',
            'name' => 'Bjørgan natursti',
            'slug' => 'bjorgan-natursti',
            'description' => 'Test',
            'status' => Route::STATUS_PUBLISHED,
            'theme' => 'nature',
        ]);

        $stop = $this->container->stopService()->create($route->id, [
            'title' => 'Velkommen',
            'slug' => 'velkommen',
            'body' => 'Her starter løypa.',
            'position' => '1',
            'status' => Stop::STATUS_PUBLISHED,
        ]);

        $response = (new QrRedirectController($this->container))->show($stop->qrToken);

        $this->assertSame(200, $response['status']);
        $this->assertStringContainsString('Velkommen', $response['body']);
        $this->assertStringContainsString('Her starter løypa.', $response['body']);
        $this->assertStringContainsString('få svaret ditt registrert', $response['body']);
        $this->assertStringNotContainsString('Neste →', $response['body']);
        $this->assertArrayNotHasKey('Location', $response['headers']);
    }

    public function testUnknownQrTokenReturnsNotFound(): void
    {
        $response = (new QrRedirectController($this->container))->show('unknown-token');

        $this->assertSame(404, $response['status']);
    }
}
