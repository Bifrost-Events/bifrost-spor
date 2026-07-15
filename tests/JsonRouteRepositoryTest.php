<?php

declare(strict_types=1);

namespace Tests;

use App\Domain\Route;

final class JsonRouteRepositoryTest extends SporTestCase
{
    private const OWNER_ID = 'participant_test_owner';

    public function testCanCreateAndReadRoute(): void
    {
        $route = $this->container->routeService()->create([
            'owner_id' => self::OWNER_ID,
            'name' => 'Testløype',
            'slug' => 'testloype',
            'description' => 'Beskrivelse',
            'status' => Route::STATUS_PUBLISHED,
            'theme' => 'nature',
        ]);

        $found = $this->container->routeRepository()->findById($route->id);
        $this->assertNotNull($found);
        $this->assertSame('Testløype', $found->name);
        $this->assertSame(self::OWNER_ID, $found->ownerId);
    }

    public function testCanUpdateRouteWithoutDuplicate(): void
    {
        $route = $this->container->routeService()->create([
            'owner_id' => self::OWNER_ID,
            'name' => 'Første',
            'slug' => 'forste',
            'description' => '',
            'status' => Route::STATUS_DRAFT,
            'theme' => 'default',
        ]);

        $updated = new Route(
            $route->id,
            self::OWNER_ID,
            'Oppdatert navn',
            'forste',
            'Ny beskrivelse',
            Route::STATUS_PUBLISHED,
            'culture',
            $route->createdAt,
            (new \DateTimeImmutable('now'))->format('c'),
        );

        $this->container->routeRepository()->save($updated);
        $all = $this->container->routeRepository()->findAll();

        $this->assertCount(1, $all);
        $this->assertSame('Oppdatert navn', $all[0]->name);
    }

    public function testCanFindRoutesByOwner(): void
    {
        $this->container->routeService()->create([
            'owner_id' => 'owner_a',
            'name' => 'A',
            'slug' => 'a',
            'description' => '',
            'status' => Route::STATUS_DRAFT,
            'theme' => 'default',
        ]);
        $this->container->routeService()->create([
            'owner_id' => 'owner_b',
            'name' => 'B',
            'slug' => 'b',
            'description' => '',
            'status' => Route::STATUS_DRAFT,
            'theme' => 'default',
        ]);

        $this->assertCount(1, $this->container->routeRepository()->findByOwnerId('owner_a'));
    }
}
