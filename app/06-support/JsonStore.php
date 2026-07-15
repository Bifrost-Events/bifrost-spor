<?php

declare(strict_types=1);

namespace App\Support;

final class JsonStore
{
    private const DEFAULT_SCHEMA = [
        'schema_version' => 1,
        'items' => [],
    ];

    public function __construct(
        private readonly string $filePath,
        private readonly int $expectedSchemaVersion = 1,
    ) {
    }

    /**
     * @return array{schema_version: int, items: array<int, array<string, mixed>>}
     */
    public function read(): array
    {
        if (!file_exists($this->filePath)) {
            return self::DEFAULT_SCHEMA;
        }

        $contents = file_get_contents($this->filePath);
        if ($contents === false) {
            throw new StorageException('Kunne ikke lese datafil: ' . $this->filePath);
        }

        if (trim($contents) === '') {
            return self::DEFAULT_SCHEMA;
        }

        try {
            $decoded = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new StorageException('Ugyldig JSON i ' . basename($this->filePath) . ': ' . $e->getMessage(), 0, $e);
        }

        if (!is_array($decoded)) {
            throw new StorageException('JSON-roten må være et objekt i ' . basename($this->filePath));
        }

        $version = (int) ($decoded['schema_version'] ?? 0);
        if ($version !== $this->expectedSchemaVersion) {
            throw new StorageException(
                'Ukjent schema_version ' . $version . ' i ' . basename($this->filePath)
                . ' (forventet ' . $this->expectedSchemaVersion . ')'
            );
        }

        if (!isset($decoded['items']) || !is_array($decoded['items'])) {
            throw new StorageException('JSON mangler items-array i ' . basename($this->filePath));
        }

        /** @var array{schema_version: int, items: array<int, array<string, mixed>>} */
        return [
            'schema_version' => $version,
            'items' => array_values($decoded['items']),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $items
     */
    public function writeItems(array $items): void
    {
        $this->write([
            'schema_version' => $this->expectedSchemaVersion,
            'items' => array_values($items),
        ]);
    }

    /**
     * @param array{schema_version: int, items: array<int, array<string, mixed>>} $data
     */
    public function write(array $data): void
    {
        $dir = dirname($this->filePath);
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new StorageException('Kunne ikke opprette mappe: ' . $dir);
        }

        $json = json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
        );

        $handle = fopen($this->filePath, 'c+');
        if ($handle === false) {
            throw new StorageException('Kunne ikke åpne datafil for skriving: ' . $this->filePath);
        }

        try {
            if (!flock($handle, LOCK_EX)) {
                throw new StorageException('Kunne ikke låse datafil: ' . $this->filePath);
            }
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }

        $tmpPath = $this->filePath . '.' . bin2hex(random_bytes(8)) . '.tmp';
        if (file_put_contents($tmpPath, $json) === false) {
            throw new StorageException('Kunne ikke skrive midlertidig fil: ' . $tmpPath);
        }

        $verify = file_get_contents($tmpPath);
        if ($verify === false) {
            @unlink($tmpPath);
            throw new StorageException('Kunne ikke lese midlertidig fil for validering.');
        }

        try {
            json_decode($verify, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            @unlink($tmpPath);
            throw new StorageException('Midlertidig fil inneholder ugyldig JSON.', 0, $e);
        }

        if (!rename($tmpPath, $this->filePath)) {
            @unlink($tmpPath);
            throw new StorageException('Kunne ikke erstatte datafil atomisk: ' . $this->filePath);
        }
    }
}
