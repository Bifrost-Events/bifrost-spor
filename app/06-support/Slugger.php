<?php

declare(strict_types=1);

namespace App\Support;

final class Slugger
{
    public static function slugify(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');
        $replacements = [
            'æ' => 'ae',
            'ø' => 'o',
            'å' => 'a',
            'ä' => 'a',
            'ö' => 'o',
            'ü' => 'u',
        ];
        $text = strtr($text, $replacements);
        $text = preg_replace('/[^a-z0-9]+/u', '-', $text) ?? '';
        $text = trim($text, '-');

        return $text !== '' ? $text : 'item';
    }

    public static function isValid(string $slug): bool
    {
        return $slug !== '' && (bool) preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug);
    }
}
