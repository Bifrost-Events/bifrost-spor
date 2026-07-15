<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Domain\Route;

interface RouteRepository
{
    /** @return Route[] */
    public function findAll(): array;

    /** @return Route[] */
    public function findByOwnerId(string $ownerId): array;

    public function findById(string $id): ?Route;

    public function findBySlug(string $slug): ?Route;

    public function save(Route $route): void;

    public function delete(string $id): void;
}
