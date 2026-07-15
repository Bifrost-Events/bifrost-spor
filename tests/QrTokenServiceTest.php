<?php

declare(strict_types=1);

namespace Tests;

use App\Domain\Route;
use App\Domain\Stop;

final class QrTokenServiceTest extends SporTestCase
{
    public function testGeneratedTokensAreUnique(): void
    {
        $route = $this->container->routeService()->create([
            'owner_id' => 'participant_test',
            'name' => 'Løype',
            'slug' => 'loype',
            'description' => '',
            'status' => Route::STATUS_PUBLISHED,
            'theme' => 'default',
        ]);

        $tokens = [];
        for ($i = 0; $i < 5; $i++) {
            $stop = $this->container->stopService()->create($route->id, [
                'title' => 'Post ' . $i,
                'slug' => 'post-' . $i,
                'body' => 'Tekst',
                'position' => (string) ($i + 1),
                'status' => Stop::STATUS_PUBLISHED,
            ]);
            $tokens[] = $stop->qrToken;
        }

        $this->assertCount(5, array_unique($tokens));
        foreach ($tokens as $token) {
            $this->assertMatchesRegularExpression('/^[23456789ABCDEFGHJKMNPQRSTUVWXYZ]{8}$/', $token);
        }
    }
}
