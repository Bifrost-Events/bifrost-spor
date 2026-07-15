<?php

declare(strict_types=1);

namespace App\Repositories\Json;

use App\Contracts\Repositories\AnswerRepository;
use App\Domain\Answer;
use App\Support\JsonStore;
use App\Support\ValidationException;

final class JsonAnswerRepository implements AnswerRepository
{
    public function __construct(
        private readonly JsonStore $store,
    ) {
    }

    public function findByRouteId(string $routeId): array
    {
        $answers = [];
        foreach ($this->store->read()['items'] as $row) {
            if (($row['route_id'] ?? '') === $routeId) {
                $answers[] = Answer::fromArray($row);
            }
        }

        return $answers;
    }

    public function findByParticipantAndRoute(string $participantId, string $routeId): array
    {
        return array_values(array_filter(
            $this->findByRouteId($routeId),
            static fn (Answer $answer): bool => $answer->participantId === $participantId
        ));
    }

    public function findByParticipantAndStop(string $participantId, string $stopId): ?Answer
    {
        foreach ($this->store->read()['items'] as $row) {
            if (($row['participant_id'] ?? '') === $participantId && ($row['stop_id'] ?? '') === $stopId) {
                return Answer::fromArray($row);
            }
        }

        return null;
    }

    public function save(Answer $answer): void
    {
        $answer->validate();
        $items = $this->store->read()['items'];

        foreach ($items as $row) {
            if (($row['participant_id'] ?? '') === $answer->participantId && ($row['stop_id'] ?? '') === $answer->stopId) {
                throw new ValidationException('Du har allerede svart på dette spørsmålet.');
            }
        }

        $items[] = $answer->toArray();
        $this->store->writeItems($items);
    }
}
