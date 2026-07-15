<?php

declare(strict_types=1);

namespace App\Repositories\Json;

use App\Contracts\Repositories\ParticipantRepository;
use App\Domain\Participant;
use App\Support\JsonStore;
use App\Support\ValidationException;

final class JsonParticipantRepository implements ParticipantRepository
{
    public function __construct(
        private readonly JsonStore $store,
    ) {
    }

    public function findAll(): array
    {
        return array_map(
            static fn (array $row) => Participant::fromArray($row),
            $this->store->read()['items']
        );
    }

    public function findById(string $id): ?Participant
    {
        foreach ($this->store->read()['items'] as $row) {
            if (($row['id'] ?? '') === $id) {
                return Participant::fromArray($row);
            }
        }

        return null;
    }

    public function findByEmail(string $email): ?Participant
    {
        $normalized = strtolower(trim($email));
        foreach ($this->store->read()['items'] as $row) {
            if (strtolower((string) ($row['email'] ?? '')) === $normalized) {
                return Participant::fromArray($row);
            }
        }

        return null;
    }

    public function save(Participant $participant): void
    {
        $participant->validate();
        $items = $this->store->read()['items'];
        $found = false;

        foreach ($items as $index => $row) {
            if (($row['id'] ?? '') === $participant->id) {
                $items[$index] = $participant->toArray();
                $found = true;
                continue;
            }
            if (strtolower((string) ($row['email'] ?? '')) === strtolower($participant->email)) {
                throw new ValidationException('E-postadressen er allerede registrert.');
            }
        }

        if (!$found) {
            foreach ($items as $row) {
                if (strtolower((string) ($row['email'] ?? '')) === strtolower($participant->email)) {
                    throw new ValidationException('E-postadressen er allerede registrert.');
                }
            }
            $items[] = $participant->toArray();
        }

        $this->store->writeItems($items);
    }
}
