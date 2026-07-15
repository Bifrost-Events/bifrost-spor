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
        foreach (['routes.json', 'stops.json', 'participants.json', 'answers.json'] as $file) {
            $path = $this->tempDir . DIRECTORY_SEPARATOR . $file;
            if (is_file($path)) {
                unlink($path);
            }
        }
        if (is_dir($this->tempDir)) {
            rmdir($this->tempDir);
        }
    }
}
