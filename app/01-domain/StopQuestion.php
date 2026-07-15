<?php

declare(strict_types=1);

namespace App\Domain;

use App\Support\ValidationException;

final class StopQuestion
{
    /**
     * @param QuestionOption[] $options
     */
    public function __construct(
        public readonly string $text,
        public readonly array $options,
        public readonly string $correctOptionId,
    ) {
    }

    /**
     * @param mixed $data
     */
    public static function fromMixed(mixed $data): ?self
    {
        if (!is_array($data)) {
            return null;
        }

        return self::fromArray($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $options = [];
        foreach ($data['options'] ?? [] as $option) {
            if (is_array($option)) {
                $options[] = QuestionOption::fromArray($option);
            }
        }

        return new self(
            (string) ($data['text'] ?? ''),
            $options,
            (string) ($data['correct_option_id'] ?? ''),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'text' => $this->text,
            'options' => array_map(static fn (QuestionOption $o) => $o->toArray(), $this->options),
            'correct_option_id' => $this->correctOptionId,
        ];
    }

    public function validate(): void
    {
        if (trim($this->text) === '') {
            throw new ValidationException('Spørsmålstekst er påkrevd.');
        }
        if (count($this->options) < 2) {
            throw new ValidationException('Spørsmål må ha minst to alternativer.');
        }

        $ids = [];
        foreach ($this->options as $option) {
            if (trim($option->id) === '' || trim($option->text) === '') {
                throw new ValidationException('Alle alternativer må ha id og tekst.');
            }
            if (in_array($option->id, $ids, true)) {
                throw new ValidationException('Alternativ-id-er må være unike.');
            }
            $ids[] = $option->id;
        }

        if (!in_array($this->correctOptionId, $ids, true)) {
            throw new ValidationException('Riktig alternativ finnes ikke blant valgmulighetene.');
        }
    }

    public function isCorrect(string $optionId): bool
    {
        return hash_equals($this->correctOptionId, $optionId);
    }

    public function findOption(string $optionId): ?QuestionOption
    {
        foreach ($this->options as $option) {
            if ($option->id === $optionId) {
                return $option;
            }
        }

        return null;
    }
}
