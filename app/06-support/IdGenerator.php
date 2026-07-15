<?php

declare(strict_types=1);

namespace App\Support;

final class IdGenerator
{
    private const ENCODING = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';

    public static function routeId(): string
    {
        return 'route_' . self::ulid();
    }

    public static function stopId(): string
    {
        return 'stop_' . self::ulid();
    }

    public static function participantId(): string
    {
        return 'participant_' . self::ulid();
    }

    public static function answerId(): string
    {
        return 'answer_' . self::ulid();
    }

    private static function ulid(): string
    {
        $time = (int) floor(microtime(true) * 1000);
        $timeChars = '';
        for ($i = 9; $i >= 0; $i--) {
            $timeChars = self::ENCODING[$time % 32] . $timeChars;
            $time = intdiv($time, 32);
        }

        $random = random_bytes(10);
        $randomChars = '';
        $buffer = 0;
        $bits = 0;
        for ($i = 0; $i < 10; $i++) {
            $buffer = ($buffer << 8) | ord($random[$i]);
            $bits += 8;
            while ($bits >= 5) {
                $bits -= 5;
                $randomChars .= self::ENCODING[($buffer >> $bits) & 31];
            }
        }
        if ($bits > 0) {
            $randomChars .= self::ENCODING[($buffer << (5 - $bits)) & 31];
        }

        return substr($timeChars . $randomChars, 0, 26);
    }
}
