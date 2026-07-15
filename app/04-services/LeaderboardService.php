<?php

declare(strict_types=1);

namespace App\Service;

use App\Contracts\Repositories\AnswerRepository;
use App\Contracts\Repositories\ParticipantRepository;
use App\Contracts\Repositories\StopRepository;

final class LeaderboardService
{
    public function __construct(
        private readonly AnswerRepository $answers,
        private readonly ParticipantRepository $participants,
        private readonly StopRepository $stops,
    ) {
    }

    /**
     * @return list<array{participant_id: string, name: string, correct: int, total_answered: int, total_questions: int, score_percent: int, last_answered_at: ?string}>
     */
    public function forRoute(string $routeId): array
    {
        $totalQuestions = count(array_filter(
            $this->stops->findByRouteId($routeId),
            static fn ($stop) => $stop->question !== null && $stop->status === \App\Domain\Stop::STATUS_PUBLISHED
        ));

        /** @var array<string, array{participant_id: string, name: string, correct: int, total_answered: int, total_questions: int, score_percent: int, last_answered_at: ?string}> $rows */
        $rows = [];

        foreach ($this->answers->findByRouteId($routeId) as $answer) {
            if (!isset($rows[$answer->participantId])) {
                $participant = $this->participants->findById($answer->participantId);
                $rows[$answer->participantId] = [
                    'participant_id' => $answer->participantId,
                    'name' => $participant?->name ?? 'Ukjent',
                    'correct' => 0,
                    'total_answered' => 0,
                    'total_questions' => $totalQuestions,
                    'score_percent' => 0,
                    'last_answered_at' => null,
                ];
            }

            $rows[$answer->participantId]['total_answered']++;
            if ($answer->isCorrect) {
                $rows[$answer->participantId]['correct']++;
            }

            $last = $rows[$answer->participantId]['last_answered_at'];
            if ($last === null || $answer->answeredAt > $last) {
                $rows[$answer->participantId]['last_answered_at'] = $answer->answeredAt;
            }
        }

        foreach ($rows as &$row) {
            $row['score_percent'] = $row['total_answered'] > 0
                ? (int) round(($row['correct'] / $row['total_answered']) * 100)
                : 0;
        }
        unset($row);

        $result = array_values($rows);
        usort($result, static function (array $a, array $b): int {
            if ($a['correct'] !== $b['correct']) {
                return $b['correct'] <=> $a['correct'];
            }
            if ($a['total_answered'] !== $b['total_answered']) {
                return $b['total_answered'] <=> $a['total_answered'];
            }

            return strcmp((string) ($a['last_answered_at'] ?? ''), (string) ($b['last_answered_at'] ?? ''));
        });

        return $result;
    }
}
