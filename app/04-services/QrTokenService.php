<?php

declare(strict_types=1);

namespace App\Service;

use App\Contracts\Repositories\StopRepository;

final class QrTokenService
{
    private const ALPHABET = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';
    private const LENGTH = 8;

    public function __construct(
        private readonly StopRepository $stops,
    ) {
    }

    public function generateUnique(): string
    {
        for ($attempt = 0; $attempt < 50; $attempt++) {
            $token = $this->generate();
            if (!$this->stops->isQrTokenTaken($token)) {
                return $token;
            }
        }

        throw new \RuntimeException('Kunne ikke generere unikt QR-token.');
    }

    private function generate(): string
    {
        $token = '';
        $max = strlen(self::ALPHABET) - 1;
        for ($i = 0; $i < self::LENGTH; $i++) {
            $token .= self::ALPHABET[random_int(0, $max)];
        }

        return $token;
    }
}
