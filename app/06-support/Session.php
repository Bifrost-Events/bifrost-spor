<?php

declare(strict_types=1);

namespace App\Support;

final class Session
{
    public const SESSION_NAME = 'BIFROSTSPOR';

    private const AUTH_KEY = 'bifrost_spor_auth';
    private const PARTICIPANT_KEY = 'bifrost_spor_participant';
    private const FLASH_KEY = 'bifrost_spor_flash';
    private const CSRF_KEY = 'bifrost_spor_csrf';
    private const GUEST_ANSWERS_KEY = 'bifrost_spor_guest_answers';

    /** @var bool|null */
    private static ?bool $configured = null;

    public static function startRequired(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        self::configureCookieParams();
        session_name(self::SESSION_NAME);
        session_start();
    }

    public static function setAuth(bool $authenticated): void
    {
        self::startRequired();
        $_SESSION[self::AUTH_KEY] = $authenticated;
    }

    public static function isAuthenticated(): bool
    {
        self::startRequired();

        return !empty($_SESSION[self::AUTH_KEY]);
    }

    public static function clearAuth(): void
    {
        self::startRequired();
        unset($_SESSION[self::AUTH_KEY]);
    }

    /** @param array{id: string, name: string, email: string} $participant */
    public static function setParticipant(array $participant): void
    {
        self::startRequired();
        $_SESSION[self::PARTICIPANT_KEY] = $participant;
    }

    /** @return array{id: string, name: string, email: string}|null */
    public static function getParticipant(): ?array
    {
        self::startRequired();
        $participant = $_SESSION[self::PARTICIPANT_KEY] ?? null;

        return is_array($participant) ? [
            'id' => (string) ($participant['id'] ?? ''),
            'name' => (string) ($participant['name'] ?? ''),
            'email' => (string) ($participant['email'] ?? ''),
        ] : null;
    }

    public static function isParticipantLoggedIn(): bool
    {
        $participant = self::getParticipant();

        return $participant !== null && ($participant['id'] ?? '') !== '';
    }

    public static function clearParticipant(): void
    {
        self::startRequired();
        unset($_SESSION[self::PARTICIPANT_KEY]);
    }

    public static function setFlash(string $type, string $message): void
    {
        self::startRequired();
        $_SESSION[self::FLASH_KEY] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    /** @return array{type: string, message: string}|null */
    public static function pullFlash(): ?array
    {
        self::startRequired();
        $flash = $_SESSION[self::FLASH_KEY] ?? null;
        unset($_SESSION[self::FLASH_KEY]);
        if (!is_array($flash)) {
            return null;
        }

        return [
            'type' => (string) ($flash['type'] ?? 'info'),
            'message' => (string) ($flash['message'] ?? ''),
        ];
    }

    public static function csrfToken(): string
    {
        self::startRequired();
        if (empty($_SESSION[self::CSRF_KEY])) {
            $_SESSION[self::CSRF_KEY] = bin2hex(random_bytes(32));
        }

        return (string) $_SESSION[self::CSRF_KEY];
    }

    public static function validateCsrf(?string $token): bool
    {
        self::startRequired();
        $expected = (string) ($_SESSION[self::CSRF_KEY] ?? '');

        return $expected !== '' && is_string($token) && hash_equals($expected, $token);
    }

    public static function setGuestAnswer(string $stopId, string $selectedOptionId, bool $isCorrect): void
    {
        self::startRequired();
        if (!isset($_SESSION[self::GUEST_ANSWERS_KEY]) || !is_array($_SESSION[self::GUEST_ANSWERS_KEY])) {
            $_SESSION[self::GUEST_ANSWERS_KEY] = [];
        }

        $_SESSION[self::GUEST_ANSWERS_KEY][$stopId] = [
            'selected_option_id' => $selectedOptionId,
            'is_correct' => $isCorrect,
        ];
    }

    /** @return array{selected_option_id: string, is_correct: bool}|null */
    public static function getGuestAnswer(string $stopId): ?array
    {
        self::startRequired();
        $answer = $_SESSION[self::GUEST_ANSWERS_KEY][$stopId] ?? null;
        if (!is_array($answer)) {
            return null;
        }

        return [
            'selected_option_id' => (string) ($answer['selected_option_id'] ?? ''),
            'is_correct' => (bool) ($answer['is_correct'] ?? false),
        ];
    }

    /** @return array<string, array{selected_option_id: string, is_correct: bool}> */
    public static function pullGuestAnswers(): array
    {
        self::startRequired();
        $answers = $_SESSION[self::GUEST_ANSWERS_KEY] ?? [];
        unset($_SESSION[self::GUEST_ANSWERS_KEY]);
        if (!is_array($answers)) {
            return [];
        }

        $normalized = [];
        foreach ($answers as $stopId => $answer) {
            if (!is_string($stopId) || !is_array($answer)) {
                continue;
            }
            $normalized[$stopId] = [
                'selected_option_id' => (string) ($answer['selected_option_id'] ?? ''),
                'is_correct' => (bool) ($answer['is_correct'] ?? false),
            ];
        }

        return $normalized;
    }

    private static function configureCookieParams(): void
    {
        if (self::$configured === true) {
            return;
        }

        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        self::$configured = true;
    }
}
