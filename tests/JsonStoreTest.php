<?php

declare(strict_types=1);

namespace Tests;

use App\Support\JsonStore;
use App\Support\StorageException;
use PHPUnit\Framework\TestCase;

final class JsonStoreTest extends TestCase
{
    private string $tempFile;

    protected function setUp(): void
    {
        $this->tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'json-store-' . bin2hex(random_bytes(8)) . '.json';
    }

    protected function tearDown(): void
    {
        if (is_file($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    public function testInvalidJsonThrowsStorageException(): void
    {
        file_put_contents($this->tempFile, '{invalid json');
        $store = new JsonStore($this->tempFile);

        $this->expectException(StorageException::class);
        $this->expectExceptionMessage('Ugyldig JSON');

        $store->read();
    }
}
