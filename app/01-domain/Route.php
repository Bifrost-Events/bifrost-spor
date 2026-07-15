<?php

declare(strict_types=1);

namespace App\Domain;

use App\Support\Config;
use App\Support\Slugger;
use App\Support\ValidationException;

final class Route
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    public function __construct(
        public readonly string $id,
        public string $ownerId,
        public string $name,
        public string $slug,
        public string $description,
        public string $status,
        public string $theme,
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
            (string) ($data['owner_id'] ?? ''),
            (string) ($data['name'] ?? ''),
            (string) ($data['slug'] ?? ''),
            (string) ($data['description'] ?? ''),
            (string) ($data['status'] ?? self::STATUS_DRAFT),
            (string) ($data['theme'] ?? 'default'),
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
            'owner_id' => $this->ownerId,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => $this->status,
            'theme' => $this->theme,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    public function validate(): void
    {
        if (trim($this->ownerId) === '') {
            throw new ValidationException('Eier av løypa mangler.');
        }
        if (trim($this->name) === '') {
            throw new ValidationException('Navn er påkrevd.');
        }
        if (!Slugger::isValid($this->slug)) {
            throw new ValidationException('Slug er ugyldig. Bruk kun små bokstaver, tall og bindestrek.');
        }
        $statuses = Config::get('app.route_statuses', [self::STATUS_DRAFT, self::STATUS_PUBLISHED]);
        if (!in_array($this->status, $statuses, true)) {
            throw new ValidationException('Ugyldig status for løype.');
        }
    }
}
