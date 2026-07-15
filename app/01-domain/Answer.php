<?php

declare(strict_types=1);

namespace App\Domain;

use App\Support\ValidationException;

final class Answer
{
    public function __construct(
        public readonly string $id,
        public readonly string $participantId,
        public readonly string $routeId,
        public readonly string $stopId,
        public readonly string $selectedOptionId,
        public readonly bool $isCorrect,
        public readonly string $answeredAt,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['id'] ?? ''),
            (string) ($data['participant_id'] ?? ''),
            (string) ($data['route_id'] ?? ''),
            (string) ($data['stop_id'] ?? ''),
            (string) ($data['selected_option_id'] ?? ''),
            (bool) ($data['is_correct'] ?? false),
            (string) ($data['answered_at'] ?? ''),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'participant_id' => $this->participantId,
            'route_id' => $this->routeId,
            'stop_id' => $this->stopId,
            'selected_option_id' => $this->selectedOptionId,
            'is_correct' => $this->isCorrect,
            'answered_at' => $this->answeredAt,
        ];
    }

    public function validate(): void
    {
        if ($this->participantId === '' || $this->routeId === '' || $this->stopId === '') {
            throw new ValidationException('Svar mangler nødvendige referanser.');
        }
        if ($this->selectedOptionId === '') {
            throw new ValidationException('Ingen alternativ valgt.');
        }
    }
}
