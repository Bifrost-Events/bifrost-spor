<?php

declare(strict_types=1);

namespace Tests;

use App\Controller\PrintController;
use App\Support\Session;

final class PrintControllerTest extends SporTestCase
{
    public function testOwnerCanOpenRoutePrintView(): void
    {
        $this->container->participantAuthService()->register('Eier', 'eier@example.com', 'passord1234', 'passord1234');
        $participant = $this->container->participantRepository()->findByEmail('eier@example.com');
        $this->assertNotNull($participant);
        Session::setParticipant([
            'id' => $participant->id,
            'name' => $participant->name,
            'email' => $participant->email,
        ]);

        $route = $this->container->routeService()->create([
            'owner_id' => $participant->id,
            'name' => 'Skiltløype',
            'slug' => 'skiltloype',
            'description' => '',
            'status' => \App\Domain\Route::STATUS_PUBLISHED,
            'theme' => 'default',
        ]);

        $this->container->stopService()->create($route->id, [
            'title' => 'Post 1',
            'slug' => 'post-1',
            'body' => 'Innhold',
            'position' => '1',
            'status' => \App\Domain\Stop::STATUS_PUBLISHED,
        ]);

        $response = (new PrintController($this->container))->route($route->id);

        $this->assertSame(200, $response['status']);
        $this->assertStringContainsString('Skiltløype', $response['body']);
        $this->assertStringContainsString('Skann QR-koden', $response['body']);
        $this->assertStringContainsString('no-print', $response['body']);
        $this->assertStringContainsString('Skilt per side', $response['body']);
        $this->assertStringContainsString('per-page-1', $response['body']);
    }

    public function testRoutePrintViewUsesPerPageQueryParam(): void
    {
        $this->container->participantAuthService()->register('Eier', 'eier2@example.com', 'passord1234', 'passord1234');
        $participant = $this->container->participantRepository()->findByEmail('eier2@example.com');
        $this->assertNotNull($participant);
        Session::setParticipant([
            'id' => $participant->id,
            'name' => $participant->name,
            'email' => $participant->email,
        ]);

        $route = $this->container->routeService()->create([
            'owner_id' => $participant->id,
            'name' => 'Flerskilt',
            'slug' => 'flerskilt',
            'description' => '',
            'status' => \App\Domain\Route::STATUS_PUBLISHED,
            'theme' => 'default',
        ]);

        foreach (['Post 1', 'Post 2', 'Post 3'] as $index => $title) {
            $this->container->stopService()->create($route->id, [
                'title' => $title,
                'slug' => 'post-' . ($index + 1),
                'body' => 'Innhold',
                'position' => (string) ($index + 1),
                'status' => \App\Domain\Stop::STATUS_PUBLISHED,
            ]);
        }

        $_GET['per_page'] = '2';

        try {
            $response = (new PrintController($this->container))->route($route->id);
        } finally {
            unset($_GET['per_page']);
        }

        $this->assertSame(200, $response['status']);
        $this->assertStringContainsString('per-page-2', $response['body']);
        $this->assertStringContainsString('2 per side', $response['body']);
        $this->assertSame(2, substr_count($response['body'], 'class="print-page"'));
    }
}
