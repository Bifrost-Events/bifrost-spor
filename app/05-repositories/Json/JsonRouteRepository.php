<?php

declare(strict_types=1);

namespace App\Repositories\Json;

use App\Contracts\Repositories\RouteRepository;
use App\Domain\Route;
use App\Support\JsonStore;
use App\Support\ValidationException;

final class JsonRouteRepository implements RouteRepository
{
    public function __construct(
        private readonly JsonStore $store,
    ) {
    }

    public function findAll(): array
    {
        $items = $this->store->read()['items'];

        return array_map(static fn (array $row) => Route::fromArray($row), $items);
    }

    public function findByOwnerId(string $ownerId): array
    {
        return array_values(array_filter(
            $this->findAll(),
            static fn (Route $route): bool => $route->ownerId === $ownerId
        ));
    }

    public function findById(string $id): ?Route
    {
        foreach ($this->store->read()['items'] as $row) {
            if (($row['id'] ?? '') === $id) {
                return Route::fromArray($row);
            }
        }

        return null;
    }

    public function findBySlug(string $slug): ?Route
    {
        foreach ($this->store->read()['items'] as $row) {
            if (($row['slug'] ?? '') === $slug) {
                return Route::fromArray($row);
            }
        }

        return null;
    }

    public function save(Route $route): void
    {
        $route->validate();
        $data = $this->store->read();
        $items = $data['items'];
        $found = false;

        foreach ($items as $index => $row) {
            if (($row['id'] ?? '') === $route->id) {
                $items[$index] = $route->toArray();
                $found = true;
                continue;
            }
            if (($row['slug'] ?? '') === $route->slug) {
                throw new ValidationException('Slug er allerede i bruk for en annen løype.');
            }
        }

        if (!$found) {
            foreach ($items as $row) {
                if (($row['slug'] ?? '') === $route->slug) {
                    throw new ValidationException('Slug er allerede i bruk for en annen løype.');
                }
            }
            $items[] = $route->toArray();
        }

        $this->store->writeItems($items);
    }

    public function delete(string $id): void
    {
        $items = array_values(array_filter(
            $this->store->read()['items'],
            static fn (array $row): bool => ($row['id'] ?? '') !== $id
        ));
        $this->store->writeItems($items);
    }
}
