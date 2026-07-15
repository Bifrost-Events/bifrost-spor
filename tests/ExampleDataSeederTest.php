<?php

declare(strict_types=1);

namespace Tests;

use App\Support\ExampleDataSeeder;
use App\Support\JsonStore;

final class ExampleDataSeederTest extends SporTestCase
{
    public function testSupplementsMissingRoutesWithoutOverwritingExistingSlug(): void
    {
        $routeStore = new JsonStore($this->tempDir . DIRECTORY_SEPARATOR . 'routes.json');
        $routeStore->writeItems([
            [
                'id' => 'route_existing',
                'owner_id' => 'participant_x',
                'name' => 'Eksisterende',
                'slug' => 'bjorgan-natursti',
                'description' => 'keep me',
                'status' => 'published',
                'theme' => 'nature',
                'created_at' => '2026-07-14T00:00:00+02:00',
                'updated_at' => '2026-07-14T00:00:00+02:00',
            ],
        ]);

        $base = $this->tempDir . DIRECTORY_SEPARATOR . 'pkg';
        $examples = $base . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'examples';
        mkdir($examples, 0775, true);
        file_put_contents($examples . DIRECTORY_SEPARATOR . 'routes.json', json_encode([
            'schema_version' => 1,
            'items' => [
                [
                    'id' => 'route_example_same_slug',
                    'owner_id' => 'participant_x',
                    'name' => 'Skal ikke erstatte',
                    'slug' => 'bjorgan-natursti',
                    'description' => 'nope',
                    'status' => 'published',
                    'theme' => 'nature',
                    'created_at' => '2026-07-15T00:00:00+02:00',
                    'updated_at' => '2026-07-15T00:00:00+02:00',
                ],
                [
                    'id' => 'route_new',
                    'owner_id' => 'participant_x',
                    'name' => 'Ny løype',
                    'slug' => 'ny-loype',
                    'description' => 'add me',
                    'status' => 'published',
                    'theme' => 'urban',
                    'created_at' => '2026-07-15T00:00:00+02:00',
                    'updated_at' => '2026-07-15T00:00:00+02:00',
                ],
            ],
        ], JSON_THROW_ON_ERROR));
        file_put_contents($examples . DIRECTORY_SEPARATOR . 'stops.json', json_encode([
            'schema_version' => 1,
            'items' => [],
        ], JSON_THROW_ON_ERROR));

        ExampleDataSeeder::seedIfNeeded($base, $this->tempDir);

        $items = $routeStore->read()['items'];
        $this->assertCount(2, $items);
        $bySlug = [];
        foreach ($items as $row) {
            $bySlug[$row['slug']] = $row;
        }
        $this->assertSame('Eksisterende', $bySlug['bjorgan-natursti']['name']);
        $this->assertSame('Ny løype', $bySlug['ny-loype']['name']);
    }

    public function testCopiesExamplesWhenDataMissing(): void
    {
        $base = $this->tempDir . DIRECTORY_SEPARATOR . 'pkg';
        $examples = $base . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'examples';
        $data = $this->tempDir . DIRECTORY_SEPARATOR . 'empty-data';
        mkdir($examples, 0775, true);
        mkdir($data, 0775, true);

        file_put_contents($examples . DIRECTORY_SEPARATOR . 'routes.json', json_encode([
            'schema_version' => 1,
            'items' => [
                [
                    'id' => 'route_seed1',
                    'owner_id' => 'participant_x',
                    'name' => 'Seedløype',
                    'slug' => 'seedloype',
                    'description' => '',
                    'status' => 'published',
                    'theme' => 'nature',
                    'created_at' => '2026-07-15T00:00:00+02:00',
                    'updated_at' => '2026-07-15T00:00:00+02:00',
                ],
            ],
        ], JSON_THROW_ON_ERROR));
        file_put_contents($examples . DIRECTORY_SEPARATOR . 'stops.json', json_encode([
            'schema_version' => 1,
            'items' => [
                [
                    'id' => 'stop_seed1',
                    'route_id' => 'route_seed1',
                    'title' => 'Start',
                    'slug' => 'start',
                    'body' => 'Hei',
                    'question' => null,
                    'image_path' => null,
                    'position' => 1,
                    'qr_token' => 'ABCD1234',
                    'status' => 'published',
                    'latitude' => null,
                    'longitude' => null,
                    'created_at' => '2026-07-15T00:00:00+02:00',
                    'updated_at' => '2026-07-15T00:00:00+02:00',
                ],
            ],
        ], JSON_THROW_ON_ERROR));

        ExampleDataSeeder::seedIfNeeded($base, $data);

        $routes = (new JsonStore($data . DIRECTORY_SEPARATOR . 'routes.json'))->read()['items'];
        $stops = (new JsonStore($data . DIRECTORY_SEPARATOR . 'stops.json'))->read()['items'];
        $this->assertCount(1, $routes);
        $this->assertCount(1, $stops);
        $this->assertSame('seedloype', $routes[0]['slug']);
    }
}
