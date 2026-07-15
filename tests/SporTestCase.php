<?php

declare(strict_types=1);

namespace Tests;

use App\Support\Config;
use App\Support\Container;
use PHPUnit\Framework\TestCase;

abstract class SporTestCase extends TestCase
{
    protected string $tempDir;
    protected Container $container;

    protected function setUp(): void
    {
        Config::load('app');
        $this->tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bifrost-spor-' . bin2hex(random_bytes(8));
        mkdir($this->tempDir, 0775, true);
        $this->container = (new Container())->withDataPath($this->tempDir);
    }

    protected function tearDown(): void
    {
        $this->removeTree($this->tempDir);
    }

    private function removeTree(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }
        if (is_file($path)) {
            unlink($path);
            return;
        }
        $items = scandir($path);
        if ($items === false) {
            return;
        }
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $this->removeTree($path . DIRECTORY_SEPARATOR . $item);
        }
        rmdir($path);
    }
}
