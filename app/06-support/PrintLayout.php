<?php

declare(strict_types=1);

namespace App\Support;

final class PrintLayout
{
    public const DEFAULT_PER_PAGE = 1;

    /** @var list<int> */
    public const ALLOWED = [1, 2, 4];

    public static function parsePerPage(mixed $value): int
    {
        $parsed = filter_var($value, FILTER_VALIDATE_INT);

        if ($parsed !== false && in_array($parsed, self::ALLOWED, true)) {
            return $parsed;
        }

        return self::DEFAULT_PER_PAGE;
    }

    /**
     * @param list<T> $signs
     * @return list<list<T>>
     *
     * @template T
     */
    public static function paginate(array $signs, int $perPage): array
    {
        if ($signs === []) {
            return [];
        }

        return array_chunk($signs, max(1, $perPage));
    }

    public static function pageCount(int $signCount, int $perPage): int
    {
        if ($signCount === 0) {
            return 0;
        }

        return (int) ceil($signCount / max(1, $perPage));
    }
}
