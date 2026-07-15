<?php

declare(strict_types=1);

namespace App\Service;

use App\Contracts\Repositories\RouteRepository;
use App\Contracts\Repositories\StopRepository;
use App\Domain\QuestionOption;
use App\Domain\Stop;
use App\Domain\StopQuestion;
use App\Support\IdGenerator;
use App\Support\Slugger;
use App\Support\ValidationException;

final class StopService
{
    public function __construct(
        private readonly RouteRepository $routes,
        private readonly StopRepository $stops,
        private readonly QrTokenService $qrTokens,
    ) {
    }

    /** @return Stop[] */
    public function listForRoute(string $routeId): array
    {
        return $this->stops->findByRouteId($routeId);
    }

    public function findPublishedBySlug(string $routeSlug, string $stopSlug): ?Stop
    {
        $route = $this->routes->findBySlug($routeSlug);
        if ($route === null || $route->status !== \App\Domain\Route::STATUS_PUBLISHED) {
            return null;
        }

        foreach ($this->stops->findByRouteId($route->id) as $stop) {
            if ($stop->slug === $stopSlug && $stop->status === Stop::STATUS_PUBLISHED) {
                return $stop;
            }
        }

        return null;
    }

    public function findByQrToken(string $token): ?Stop
    {
        return $this->stops->findByQrToken($token);
    }

    /**
     * @param array{title: string, slug?: string, body?: string, question_text?: string, option_1?: string, option_2?: string, option_3?: string, option_4?: string, correct_option?: string, position?: int|string, status?: string} $input
     */
    public function create(string $routeId, array $input): Stop
    {
        $route = $this->routes->findById($routeId);
        if ($route === null) {
            throw new ValidationException('Løypen finnes ikke.');
        }

        $now = $this->now();
        $title = trim($input['title'] ?? '');
        $slug = trim($input['slug'] ?? '');
        if ($slug === '') {
            $slug = Slugger::slugify($title);
        }

        $position = (int) ($input['position'] ?? 1);
        if ($position < 1) {
            $position = count($this->stops->findByRouteId($routeId)) + 1;
        }

        $stop = new Stop(
            IdGenerator::stopId(),
            $routeId,
            $title,
            $slug,
            trim($input['body'] ?? ''),
            self::buildQuestion($input),
            null,
            $position,
            $this->qrTokens->generateUnique(),
            (string) ($input['status'] ?? Stop::STATUS_DRAFT),
            null,
            null,
            $now,
            $now,
        );

        $stop->validate(true);
        $this->stops->save($stop);

        return $stop;
    }

    /**
     * @return array{prev: ?Stop, next: ?Stop}
     */
    public function neighbours(Stop $current): array
    {
        $stops = array_values(array_filter(
            $this->stops->findByRouteId($current->routeId),
            static fn (Stop $stop): bool => $stop->status === Stop::STATUS_PUBLISHED
        ));

        $prev = null;
        $next = null;
        foreach ($stops as $index => $stop) {
            if ($stop->id !== $current->id) {
                continue;
            }
            $prev = $stops[$index - 1] ?? null;
            $next = $stops[$index + 1] ?? null;
            break;
        }

        return ['prev' => $prev, 'next' => $next];
    }

    private function now(): string
    {
        return (new \DateTimeImmutable('now'))->format('c');
    }

    /**
     * @param array<string, mixed> $input
     */
    private static function buildQuestion(array $input): ?StopQuestion
    {
        $text = trim((string) ($input['question_text'] ?? ''));
        if ($text === '') {
            return null;
        }

        $optionTexts = [];
        for ($i = 1; $i <= 4; $i++) {
            $value = trim((string) ($input['option_' . $i] ?? ''));
            if ($value !== '') {
                $optionTexts[] = $value;
            }
        }

        $options = [];
        foreach ($optionTexts as $index => $optionText) {
            $options[] = new QuestionOption('opt_' . ($index + 1), $optionText);
        }

        $correctIndex = max(1, (int) ($input['correct_option'] ?? 1)) - 1;
        $correctOptionId = $options[$correctIndex]->id ?? ($options[0]->id ?? '');

        $question = new StopQuestion($text, $options, $correctOptionId);
        $question->validate();

        return $question;
    }
}
