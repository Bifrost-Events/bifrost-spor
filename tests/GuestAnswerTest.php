<?php

declare(strict_types=1);

namespace Tests;

use App\Controller\AnswerController;
use App\Controller\ParticipantAuthController;
use App\Domain\Route;
use App\Domain\Stop;
use App\Support\Session;

final class GuestAnswerTest extends SporTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Session::clearParticipant();
    }

    protected function tearDown(): void
    {
        $_POST = [];
        parent::tearDown();
    }

    public function testGuestCanAnswerWithoutLogin(): void
    {
        $route = $this->createPublishedRoute();
        $stop = $this->createPublishedStop($route->id);

        $_POST = [
            'option_id' => 'opt_2',
            '_csrf' => Session::csrfToken(),
        ];

        $response = (new AnswerController($this->container))->submit($route->slug, $stop->slug);

        $this->assertSame(302, $response['status']);
        $guestAnswer = Session::getGuestAnswer($stop->id);
        $this->assertNotNull($guestAnswer);
        $this->assertTrue($guestAnswer['is_correct']);
        $this->assertSame([], $this->container->answerRepository()->findByRouteId($route->id));
    }

    public function testGuestAnswerIsRegisteredAfterRegister(): void
    {
        $route = $this->createPublishedRoute();
        $stop = $this->createPublishedStop($route->id);

        $_POST = [
            'option_id' => 'opt_2',
            '_csrf' => Session::csrfToken(),
        ];
        (new AnswerController($this->container))->submit($route->slug, $stop->slug);

        $_POST = [
            'name' => 'Gjest',
            'email' => 'gjest@example.com',
            'password' => 'passord1234',
            'password_confirm' => 'passord1234',
            'redirect' => '/spor/' . $route->slug . '/' . $stop->slug,
            '_csrf' => Session::csrfToken(),
        ];
        (new ParticipantAuthController($this->container))->register();

        $participant = Session::getParticipant();
        $this->assertNotNull($participant);

        $saved = $this->container->answerRepository()->findByParticipantAndStop($participant['id'], $stop->id);
        $this->assertNotNull($saved);
        $this->assertTrue($saved->isCorrect);
        $this->assertNull(Session::getGuestAnswer($stop->id));
    }

    private function createPublishedRoute(): Route
    {
        return $this->container->routeService()->create([
            'owner_id' => 'participant_test',
            'name' => 'Gjesteløype',
            'slug' => 'gjesteloype',
            'description' => '',
            'status' => Route::STATUS_PUBLISHED,
            'theme' => 'default',
        ]);
    }

    private function createPublishedStop(string $routeId): Stop
    {
        return $this->container->stopService()->create($routeId, [
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
    }
}
