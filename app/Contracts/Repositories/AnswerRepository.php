<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Domain\Answer;

interface AnswerRepository
{
    /** @return Answer[] */
    public function findByRouteId(string $routeId): array;

    /** @return Answer[] */
    public function findByParticipantAndRoute(string $participantId, string $routeId): array;

    public function findByParticipantAndStop(string $participantId, string $stopId): ?Answer;

    public function save(Answer $answer): void;
}
