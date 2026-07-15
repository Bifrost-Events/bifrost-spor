<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Copies seed data from storage/examples into storage/data when runtime data is empty,
 * and supplements any example routes/stops that are missing (by slug / id).
 * Never overwrites existing rows.
 */
final class ExampleDataSeeder
{
    public static function seedIfNeeded(string $basePath, ?string $dataDir = null): void
    {
        $examplesDir = rtrim($basePath, '/\\') . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'examples';
        $dataDir = $dataDir ?? (string) Config::get(
            'app.data_path',
            rtrim($basePath, '/\\') . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'data'
        );

        if (!is_dir($examplesDir)) {
            return;
        }

        if (!is_dir($dataDir) && !mkdir($dataDir, 0775, true) && !is_dir($dataDir)) {
            throw new StorageException('Kunne ikke opprette data-mappe: ' . $dataDir);
        }

        $schema = (int) Config::get('app.schema_version', 1);
        self::mergeCollection(
            $examplesDir . DIRECTORY_SEPARATOR . 'routes.json',
            $dataDir . DIRECTORY_SEPARATOR . 'routes.json',
            $schema,
            'slug',
        );
        self::mergeCollection(
            $examplesDir . DIRECTORY_SEPARATOR . 'stops.json',
            $dataDir . DIRECTORY_SEPARATOR . 'stops.json',
            $schema,
            'id',
        );
    }

    private static function mergeCollection(
        string $exampleFile,
        string $dataFile,
        int $schema,
        string $uniqueKey,
    ): void {
        if (!is_file($exampleFile)) {
            return;
        }

        $exampleItems = self::readItems($exampleFile, $schema);
        if ($exampleItems === []) {
            return;
        }

        $store = new JsonStore($dataFile, $schema);
        $existing = $store->read()['items'];
        $known = [];
        foreach ($existing as $row) {
            $key = (string) ($row[$uniqueKey] ?? '');
            if ($key !== '') {
                $known[$key] = true;
            }
        }

        $added = false;
        foreach ($exampleItems as $row) {
            $key = (string) ($row[$uniqueKey] ?? '');
            if ($key === '' || isset($known[$key])) {
                continue;
            }
            $existing[] = $row;
            $known[$key] = true;
            $added = true;
        }

        if ($added || !is_file($dataFile)) {
            $store->writeItems($existing);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function readItems(string $file, int $expectedSchema): array
    {
        $contents = file_get_contents($file);
        if ($contents === false || trim($contents) === '') {
            return [];
        }

        try {
            $decoded = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return [];
        }

        if (!is_array($decoded) || (int) ($decoded['schema_version'] ?? 0) !== $expectedSchema) {
            return [];
        }

        if (!isset($decoded['items']) || !is_array($decoded['items'])) {
            return [];
        }

        /** @var list<array<string, mixed>> */
        return array_values(array_filter(
            $decoded['items'],
            static fn ($row): bool => is_array($row)
        ));
    }
}
