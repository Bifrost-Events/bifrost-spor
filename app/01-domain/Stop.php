<?php

declare(strict_types=1);

namespace App\Domain;

use App\Support\Config;
use App\Support\Slugger;
use App\Support\ValidationException;

final class Stop
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    public function __construct(
        public readonly string $id,
        public string $routeId,
        public string $title,
        public string $slug,
        public string $body,
        public ?StopQuestion $question,
        public ?string $imagePath,
        public int $position,
        public string $qrToken,
        public string $status,
        public ?float $latitude,
        public ?float $longitude,
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
            (string) ($data['route_id'] ?? ''),
            (string) ($data['title'] ?? ''),
            (string) ($data['slug'] ?? ''),
            (string) ($data['body'] ?? ''),
            StopQuestion::fromMixed($data['question'] ?? null),
            isset($data['image_path']) ? (is_string($data['image_path']) ? $data['image_path'] : null) : null,
            (int) ($data['position'] ?? 0),
            (string) ($data['qr_token'] ?? ''),
            (string) ($data['status'] ?? self::STATUS_DRAFT),
            isset($data['latitude']) && $data['latitude'] !== null ? (float) $data['latitude'] : null,
            isset($data['longitude']) && $data['longitude'] !== null ? (float) $data['longitude'] : null,
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
            'route_id' => $this->routeId,
            'title' => $this->title,
            'slug' => $this->slug,
            'body' => $this->body,
            'question' => $this->question?->toArray(),
            'image_path' => $this->imagePath,
            'position' => $this->position,
            'qr_token' => $this->qrToken,
            'status' => $this->status,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    public function validate(bool $routeExists): void
    {
        if (!$routeExists) {
            throw new ValidationException('Løypen finnes ikke.');
        }
        if (trim($this->title) === '') {
            throw new ValidationException('Tittel er påkrevd.');
        }
        if (!Slugger::isValid($this->slug)) {
            throw new ValidationException('Slug er ugyldig. Bruk kun små bokstaver, tall og bindestrek.');
        }
        if ($this->position < 1) {
            throw new ValidationException('Posisjon må være et positivt heltall.');
        }
        if (trim($this->qrToken) === '') {
            throw new ValidationException('QR-token mangler.');
        }
        $statuses = Config::get('app.stop_statuses', [self::STATUS_DRAFT, self::STATUS_PUBLISHED]);
        if (!in_array($this->status, $statuses, true)) {
            throw new ValidationException('Ugyldig status for post.');
        }
        if ($this->question !== null) {
            $this->question->validate();
        }
    }
}
