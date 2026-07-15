<?php

declare(strict_types=1);

namespace App\Domain;

use App\Support\ValidationException;

final class Participant
{
    public function __construct(
        public readonly string $id,
        public string $name,
        public string $email,
        public string $passwordHash,
        public readonly string $createdAt,
        public string $updatedAt,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['id'] ?? ''),
            (string) ($data['name'] ?? ''),
            (string) ($data['email'] ?? ''),
            (string) ($data['password_hash'] ?? ''),
            (string) ($data['created_at'] ?? ''),
            (string) ($data['updated_at'] ?? ''),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'password_hash' => $this->passwordHash,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    public function validate(): void
    {
        if (trim($this->name) === '') {
            throw new ValidationException('Navn er påkrevd.');
        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException('Ugyldig e-postadresse.');
        }
        if ($this->passwordHash === '') {
            throw new ValidationException('Passord-hash mangler.');
        }
    }
}
