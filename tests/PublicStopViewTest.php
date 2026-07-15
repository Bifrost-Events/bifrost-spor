<?php

declare(strict_types=1);

namespace Tests;

use App\Controller\PublicController;
use App\Domain\Route;
use App\Domain\Stop;
use App\Support\Session;

final class PublicStopViewTest extends SporTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Session::clearParticipant();
    }
    public function testStopViaQrHidesNavigationAndShowsLoginNotice(): void
    {
        $route = $this->container->routeService()->create([
            'owner_id' => 'participant_test',
            'name' => 'QR-løype',
            'slug' => 'qr-loype',
            'description' => '',
            'status' => Route::STATUS_PUBLISHED,
            'theme' => 'default',
        ]);

        $this->container->stopService()->create($route->id, [
            'title' => 'Post 1',
            'slug' => 'post-1',
            'body' => 'Innhold post 1',
            'position' => '1',
            'status' => Stop::STATUS_PUBLISHED,
        ]);

        $this->container->stopService()->create($route->id, [
            'title' => 'Post 2',
            'slug' => 'post-2',
            'body' => 'Innhold post 2',
            'position' => '2',
            'status' => Stop::STATUS_PUBLISHED,
        ]);

        $_GET['qr'] = '1';

        try {
            $response = (new PublicController($this->container))->stop('qr-loype', 'post-1');
        } finally {
            unset($_GET['qr']);
        }

        $this->assertSame(200, $response['status']);
        $this->assertStringContainsString('få svaret ditt registrert', $response['body']);
        $this->assertStringNotContainsString('Neste →', $response['body']);
        $this->assertStringNotContainsString('Resultattavle', $response['body']);
    }

    public function testStopWithoutQrShowsNavigation(): void
    {
        $route = $this->container->routeService()->create([
            'owner_id' => 'participant_test',
            'name' => 'Vanlig løype',
            'slug' => 'vanlig-loype',
            'description' => '',
            'status' => Route::STATUS_PUBLISHED,
            'theme' => 'default',
        ]);

        $this->container->stopService()->create($route->id, [
            'title' => 'Post 1',
            'slug' => 'post-1',
            'body' => 'Innhold post 1',
            'position' => '1',
            'status' => Stop::STATUS_PUBLISHED,
        ]);

        $this->container->stopService()->create($route->id, [
            'title' => 'Post 2',
            'slug' => 'post-2',
            'body' => 'Innhold post 2',
            'position' => '2',
            'status' => Stop::STATUS_PUBLISHED,
        ]);

        $response = (new PublicController($this->container))->stop('vanlig-loype', 'post-1');

        $this->assertSame(200, $response['status']);
        $this->assertStringContainsString('Neste →', $response['body']);
    }
}
