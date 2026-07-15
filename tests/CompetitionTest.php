<?php

declare(strict_types=1);

namespace Tests;

use App\Domain\Route;
use App\Domain\Stop;

final class CompetitionTest extends SporTestCase
{
    public function testParticipantCanRegisterAndAnswerCorrectly(): void
    {
        $register = $this->container->participantAuthService()->register(
            'Ola Nordmann',
            'ola@example.com',
            'hemmelig123',
            'hemmelig123',
        );
        $this->assertTrue($register['ok']);

        $participant = $this->container->participantRepository()->findByEmail('ola@example.com');
        $this->assertNotNull($participant);

        $route = $this->container->routeService()->create([
            'owner_id' => $participant->id,
            'name' => 'Konkurransesti',
            'slug' => 'konkurransesti',
            'description' => '',
            'status' => Route::STATUS_PUBLISHED,
            'theme' => 'nature',
        ]);

        $stop = $this->container->stopService()->create($route->id, [
            'title' => 'Post 1',
            'slug' => 'post-1',
            'body' => 'Tekst',
            'question_text' => 'Hva er 2+2?',
            'option_1' => '3',
            'option_2' => '4',
            'option_3' => '5',
            'option_4' => '6',
            'correct_option' => '2',
            'position' => '1',
            'status' => Stop::STATUS_PUBLISHED,
        ]);

        $answer = $this->container->answerService()->submit($participant->id, $stop, 'opt_2');
        $this->assertTrue($answer->isCorrect);

        $leaderboard = $this->container->leaderboardService()->forRoute($route->id);
        $this->assertCount(1, $leaderboard);
        $this->assertSame(1, $leaderboard[0]['correct']);
    }

    public function testDuplicateAnswerIsRejected(): void
    {
        $this->container->participantAuthService()->register('Kari', 'kari@example.com', 'passord1234', 'passord1234');
        $participant = $this->container->participantRepository()->findByEmail('kari@example.com');
        $this->assertNotNull($participant);

        $route = $this->container->routeService()->create([
            'owner_id' => $participant->id,
            'name' => 'Test',
            'slug' => 'test',
            'description' => '',
            'status' => Route::STATUS_PUBLISHED,
            'theme' => 'default',
        ]);

        $stop = $this->container->stopService()->create($route->id, [
            'title' => 'Post',
            'slug' => 'post',
            'body' => 'Tekst',
            'question_text' => 'Spørsmål?',
            'option_1' => 'A',
            'option_2' => 'B',
            'correct_option' => '1',
            'position' => '1',
            'status' => Stop::STATUS_PUBLISHED,
        ]);

        $this->container->answerService()->submit($participant->id, $stop, 'opt_1');

        $this->expectException(\App\Support\ValidationException::class);
        $this->container->answerService()->submit($participant->id, $stop, 'opt_2');
    }
}
