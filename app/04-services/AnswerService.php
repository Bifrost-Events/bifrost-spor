<?php

declare(strict_types=1);

namespace App\Service;

use App\Contracts\Repositories\AnswerRepository;
use App\Contracts\Repositories\StopRepository;
use App\Domain\Answer;
use App\Domain\Stop;
use App\Support\IdGenerator;
use App\Support\Session;
use App\Support\ValidationException;

final class AnswerService
{
    public function __construct(
        private readonly AnswerRepository $answers,
        private readonly StopRepository $stops,
    ) {
    }

    public function findForParticipantAndStop(string $participantId, string $stopId): ?Answer
    {
        return $this->answers->findByParticipantAndStop($participantId, $stopId);
    }

    public function evaluate(Stop $stop, string $selectedOptionId): bool
    {
        if ($stop->question === null) {
            throw new ValidationException('Denne posten har ikke noe spørsmål.');
        }

        if ($stop->question->findOption($selectedOptionId) === null) {
            throw new ValidationException('Ugyldig alternativ valgt.');
        }

        return $stop->question->isCorrect($selectedOptionId);
    }

    public function submit(string $participantId, Stop $stop, string $selectedOptionId): Answer
    {
        $isCorrect = $this->evaluate($stop, $selectedOptionId);

        if ($this->answers->findByParticipantAndStop($participantId, $stop->id) !== null) {
            throw new ValidationException('Du har allerede svart på dette spørsmålet.');
        }

        $answer = new Answer(
            IdGenerator::answerId(),
            $participantId,
            $stop->routeId,
            $stop->id,
            $selectedOptionId,
            $isCorrect,
            (new \DateTimeImmutable('now'))->format('c'),
        );

        $this->answers->save($answer);

        return $answer;
    }

    public function persistGuestAnswers(string $participantId): int
    {
        $guestAnswers = Session::pullGuestAnswers();
        $saved = 0;

        foreach ($guestAnswers as $stopId => $guestAnswer) {
            $stop = $this->stops->findById($stopId);
            if ($stop === null || $guestAnswer['selected_option_id'] === '') {
                continue;
            }

            try {
                $this->submit($participantId, $stop, $guestAnswer['selected_option_id']);
                $saved++;
            } catch (ValidationException) {
                continue;
            }
        }

        return $saved;
    }
}
