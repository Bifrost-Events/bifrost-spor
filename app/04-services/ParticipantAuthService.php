<?php

declare(strict_types=1);

namespace App\Service;

use App\Contracts\Repositories\ParticipantRepository;
use App\Domain\Participant;
use App\Support\IdGenerator;
use App\Support\Session;
use App\Support\ValidationException;

final class ParticipantAuthService
{
    public function __construct(
        private readonly ParticipantRepository $participants,
    ) {
    }

    /**
     * @return array{ok: bool, error?: string}
     */
    public function register(string $name, string $email, string $password, string $passwordConfirm): array
    {
        $name = trim($name);
        $email = strtolower(trim($email));

        if (strlen($password) < 8) {
            return ['ok' => false, 'error' => 'Passordet må være minst 8 tegn.'];
        }
        if (!hash_equals($password, $passwordConfirm)) {
            return ['ok' => false, 'error' => 'Passordene er ikke like.'];
        }
        if ($this->participants->findByEmail($email) !== null) {
            return ['ok' => false, 'error' => 'E-postadressen er allerede registrert.'];
        }

        $now = (new \DateTimeImmutable('now'))->format('c');
        $participant = new Participant(
            IdGenerator::participantId(),
            $name,
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            $now,
            $now,
        );

        try {
            $this->participants->save($participant);
        } catch (ValidationException $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }

        $this->loginParticipant($participant);

        return ['ok' => true];
    }

    /**
     * @return array{ok: bool, error?: string}
     */
    public function login(string $email, string $password): array
    {
        $participant = $this->participants->findByEmail(strtolower(trim($email)));
        if ($participant === null || !password_verify($password, $participant->passwordHash)) {
            return ['ok' => false, 'error' => 'Ugyldig e-post eller passord.'];
        }

        $this->loginParticipant($participant);

        return ['ok' => true];
    }

    public function logout(): void
    {
        Session::clearParticipant();
    }

    private function loginParticipant(Participant $participant): void
    {
        Session::setParticipant([
            'id' => $participant->id,
            'name' => $participant->name,
            'email' => $participant->email,
        ]);
    }
}
