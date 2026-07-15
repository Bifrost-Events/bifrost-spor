<?php

declare(strict_types=1);

namespace Tests;

use App\Domain\Route;
use App\Domain\Stop;

final class JsonStopRepositoryTest extends SporTestCase
{
    public function testCanFindStopsForRoute(): void
    {
        $route = $this->container->routeService()->create([
            'owner_id' => 'participant_test',
            'name' => 'Løype',
            'slug' => 'loype',
            'description' => '',
            'status' => Route::STATUS_PUBLISHED,
            'theme' => 'default',
        ]);

        $this->container->stopService()->create($route->id, [
            'title' => 'Post 1',
            'slug' => 'post-1',
            'body' => 'Tekst',
            'position' => '1',
            'status' => Stop::STATUS_PUBLISHED,
        ]);
        $this->container->stopService()->create($route->id, [
            'title' => 'Post 2',
            'slug' => 'post-2',
            'body' => 'Tekst 2',
            'position' => '2',
            'status' => Stop::STATUS_DRAFT,
        ]);

        $stops = $this->container->stopRepository()->findByRouteId($route->id);
        $this->assertCount(2, $stops);
        $this->assertSame('Post 1', $stops[0]->title);
        $this->assertSame('Post 2', $stops[1]->title);
    }

    public function testCanFindStopByQrToken(): void
    {
        $route = $this->container->routeService()->create([
            'owner_id' => 'participant_test',
            'name' => 'Løype',
            'slug' => 'loype',
            'description' => '',
            'status' => Route::STATUS_PUBLISHED,
            'theme' => 'default',
        ]);

        $stop = $this->container->stopService()->create($route->id, [
            'title' => 'QR-post',
            'slug' => 'qr-post',
            'body' => 'Innhold',
            'position' => '1',
            'status' => Stop::STATUS_PUBLISHED,
        ]);

        $found = $this->container->stopRepository()->findByQrToken($stop->qrToken);
        $this->assertNotNull($found);
        $this->assertSame($stop->id, $found->id);
    }
}
