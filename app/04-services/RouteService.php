<?php

declare(strict_types=1);

namespace App\Service;

use App\Contracts\Repositories\RouteRepository;
use App\Domain\Route;
use App\Support\IdGenerator;
use App\Support\Slugger;
use App\Support\ValidationException;

final class RouteService
{
    public function __construct(
        private readonly RouteRepository $routes,
    ) {
    }

    /** @return Route[] */
    public function listAll(): array
    {
        return $this->routes->findAll();
    }

    /** @return Route[] */
    public function listForOwner(string $ownerId): array
    {
        return $this->routes->findByOwnerId($ownerId);
    }

    public function findById(string $id): ?Route
    {
        return $this->routes->findById($id);
    }

    public function findPublishedBySlug(string $slug): ?Route
    {
        $route = $this->routes->findBySlug($slug);
        if ($route === null || $route->status !== Route::STATUS_PUBLISHED) {
            return null;
        }

        return $route;
    }

    /**
     * @param array{owner_id: string, name: string, slug?: string, description?: string, status?: string, theme?: string} $input
     */
    public function create(array $input): Route
    {
        $ownerId = trim((string) ($input['owner_id'] ?? ''));
        if ($ownerId === '') {
            throw new ValidationException('Du må være innlogget for å opprette en løype.');
        }

        $now = $this->now();
        $name = trim($input['name'] ?? '');
        $slug = trim($input['slug'] ?? '');
        if ($slug === '') {
            $slug = Slugger::slugify($name);
        }

        $route = new Route(
            IdGenerator::routeId(),
            $ownerId,
            $name,
            $slug,
            trim($input['description'] ?? ''),
            (string) ($input['status'] ?? Route::STATUS_DRAFT),
            (string) ($input['theme'] ?? 'default'),
            $now,
            $now,
        );

        $route->validate();
        $this->routes->save($route);

        return $route;
    }

    private function now(): string
    {
        return (new \DateTimeImmutable('now'))->format('c');
    }
}
