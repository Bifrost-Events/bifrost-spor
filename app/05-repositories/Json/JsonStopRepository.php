<?php

declare(strict_types=1);

namespace App\Repositories\Json;

use App\Contracts\Repositories\StopRepository;
use App\Domain\Stop;
use App\Support\JsonStore;
use App\Support\ValidationException;

final class JsonStopRepository implements StopRepository
{
    public function __construct(
        private readonly JsonStore $store,
    ) {
    }

    public function findByRouteId(string $routeId): array
    {
        $stops = [];
        foreach ($this->store->read()['items'] as $row) {
            if (($row['route_id'] ?? '') === $routeId) {
                $stops[] = Stop::fromArray($row);
            }
        }

        usort($stops, static fn (Stop $a, Stop $b): int => $a->position <=> $b->position);

        return $stops;
    }

    public function findById(string $id): ?Stop
    {
        foreach ($this->store->read()['items'] as $row) {
            if (($row['id'] ?? '') === $id) {
                return Stop::fromArray($row);
            }
        }

        return null;
    }

    public function findByQrToken(string $token): ?Stop
    {
        foreach ($this->store->read()['items'] as $row) {
            if (($row['qr_token'] ?? '') === $token) {
                return Stop::fromArray($row);
            }
        }

        return null;
    }

    public function save(Stop $stop): void
    {
        if ($this->isQrTokenTaken($stop->qrToken, $stop->id)) {
            throw new ValidationException('QR-token er allerede i bruk.');
        }

        $data = $this->store->read();
        $items = $data['items'];
        $found = false;

        foreach ($items as $index => $row) {
            if (($row['id'] ?? '') === $stop->id) {
                $items[$index] = $stop->toArray();
                $found = true;
                continue;
            }
            if (($row['route_id'] ?? '') === $stop->routeId && ($row['slug'] ?? '') === $stop->slug) {
                throw new ValidationException('Slug er allerede i bruk for en annen post i løypa.');
            }
        }

        if (!$found) {
            foreach ($items as $row) {
                if (($row['route_id'] ?? '') === $stop->routeId && ($row['slug'] ?? '') === $stop->slug) {
                    throw new ValidationException('Slug er allerede i bruk for en annen post i løypa.');
                }
            }
            $items[] = $stop->toArray();
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

    public function isQrTokenTaken(string $token, ?string $excludeStopId = null): bool
    {
        foreach ($this->store->read()['items'] as $row) {
            if (($row['qr_token'] ?? '') !== $token) {
                continue;
            }
            if ($excludeStopId !== null && ($row['id'] ?? '') === $excludeStopId) {
                continue;
            }

            return true;
        }

        return false;
    }
}
