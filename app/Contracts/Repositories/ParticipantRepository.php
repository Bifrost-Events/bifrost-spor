<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Domain\Participant;

interface ParticipantRepository
{
    /** @return Participant[] */
    public function findAll(): array;

    public function findById(string $id): ?Participant;

    public function findByEmail(string $email): ?Participant;

    public function save(Participant $participant): void;
}
