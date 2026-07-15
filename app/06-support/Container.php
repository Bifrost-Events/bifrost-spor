<?php

declare(strict_types=1);

namespace App\Support;

use App\Contracts\Repositories\AnswerRepository;
use App\Contracts\Repositories\ParticipantRepository;
use App\Contracts\Repositories\RouteRepository;
use App\Contracts\Repositories\StopRepository;
use App\Repositories\Json\JsonAnswerRepository;
use App\Repositories\Json\JsonParticipantRepository;
use App\Repositories\Json\JsonRouteRepository;
use App\Repositories\Json\JsonStopRepository;
use App\Service\AnswerService;
use App\Service\AuthService;
use App\Service\LeaderboardService;
use App\Service\ParticipantAuthService;
use App\Service\PrintSignService;
use App\Service\QrTokenService;
use App\Service\RouteService;
use App\Service\StopService;

final class Container
{
    private ?JsonStore $routeStore = null;
    private ?JsonStore $stopStore = null;
    private ?JsonStore $participantStore = null;
    private ?JsonStore $answerStore = null;
    private ?RouteRepository $routes = null;
    private ?StopRepository $stops = null;
    private ?ParticipantRepository $participants = null;
    private ?AnswerRepository $answers = null;
    private ?RouteService $routeService = null;
    private ?StopService $stopService = null;
    private ?QrTokenService $qrTokenService = null;
    private ?AuthService $authService = null;
    private ?ParticipantAuthService $participantAuthService = null;
    private ?AnswerService $answerService = null;
    private ?LeaderboardService $leaderboardService = null;
    private ?PrintSignService $printSignService = null;

    public function routeRepository(): RouteRepository
    {
        return $this->routes ??= new JsonRouteRepository($this->routeStore());
    }

    public function stopRepository(): StopRepository
    {
        return $this->stops ??= new JsonStopRepository($this->stopStore());
    }

    public function participantRepository(): ParticipantRepository
    {
        return $this->participants ??= new JsonParticipantRepository($this->participantStore());
    }

    public function answerRepository(): AnswerRepository
    {
        return $this->answers ??= new JsonAnswerRepository($this->answerStore());
    }

    public function routeService(): RouteService
    {
        return $this->routeService ??= new RouteService($this->routeRepository());
    }

    public function stopService(): StopService
    {
        return $this->stopService ??= new StopService(
            $this->routeRepository(),
            $this->stopRepository(),
            $this->qrTokenService(),
        );
    }

    public function qrTokenService(): QrTokenService
    {
        return $this->qrTokenService ??= new QrTokenService($this->stopRepository());
    }

    public function authService(): AuthService
    {
        return $this->authService ??= new AuthService();
    }

    public function participantAuthService(): ParticipantAuthService
    {
        return $this->participantAuthService ??= new ParticipantAuthService($this->participantRepository());
    }

    public function answerService(): AnswerService
    {
        return $this->answerService ??= new AnswerService(
            $this->answerRepository(),
            $this->stopRepository(),
        );
    }

    public function leaderboardService(): LeaderboardService
    {
        return $this->leaderboardService ??= new LeaderboardService(
            $this->answerRepository(),
            $this->participantRepository(),
            $this->stopRepository(),
        );
    }

    public function printSignService(): PrintSignService
    {
        return $this->printSignService ??= new PrintSignService();
    }

    private function routeStore(): JsonStore
    {
        return $this->routeStore ??= $this->makeStore('routes.json');
    }

    private function stopStore(): JsonStore
    {
        return $this->stopStore ??= $this->makeStore('stops.json');
    }

    private function participantStore(): JsonStore
    {
        return $this->participantStore ??= $this->makeStore('participants.json');
    }

    private function answerStore(): JsonStore
    {
        return $this->answerStore ??= $this->makeStore('answers.json');
    }

    private function makeStore(string $file): JsonStore
    {
        return new JsonStore(
            $this->dataPath($file),
            (int) Config::get('app.schema_version', 1),
        );
    }

    public function withDataPath(string $directory): self
    {
        $clone = clone $this;
        $directory = rtrim($directory, '/\\');
        $schema = (int) Config::get('app.schema_version', 1);
        $clone->routeStore = new JsonStore($directory . DIRECTORY_SEPARATOR . 'routes.json', $schema);
        $clone->stopStore = new JsonStore($directory . DIRECTORY_SEPARATOR . 'stops.json', $schema);
        $clone->participantStore = new JsonStore($directory . DIRECTORY_SEPARATOR . 'participants.json', $schema);
        $clone->answerStore = new JsonStore($directory . DIRECTORY_SEPARATOR . 'answers.json', $schema);
        $clone->routes = null;
        $clone->stops = null;
        $clone->participants = null;
        $clone->answers = null;
        $clone->routeService = null;
        $clone->stopService = null;
        $clone->qrTokenService = null;
        $clone->participantAuthService = null;
        $clone->answerService = null;
        $clone->leaderboardService = null;
        $clone->printSignService = null;

        return $clone;
    }

    private function dataPath(string $file): string
    {
        $base = (string) Config::get('app.data_path', dirname(__DIR__, 2) . '/storage/data');

        return rtrim($base, '/\\') . DIRECTORY_SEPARATOR . $file;
    }
}
