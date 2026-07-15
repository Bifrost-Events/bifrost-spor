<?php

declare(strict_types=1);

namespace App\Controller;

use App\Support\Container;
use App\Support\Csrf;
use App\Support\Response;
use App\Support\Session;

final class PublicController
{
    public function __construct(
        private readonly Container $container,
    ) {
    }

    /** @return array{status: int, headers: array<string, string>, body: string} */
    public function home(): array
    {
        $routes = array_values(array_filter(
            $this->container->routeService()->listAll(),
            static fn ($route) => $route->status === \App\Domain\Route::STATUS_PUBLISHED
        ));

        return Response::view('public/home', [
            'title' => 'Bifrost Spor',
            'routes' => $routes,
            'participant' => Session::getParticipant(),
            'flash' => Session::pullFlash(),
        ]);
    }

    /** @return array{status: int, headers: array<string, string>, body: string} */
    public function route(string $routeSlug): array
    {
        $route = $this->container->routeService()->findPublishedBySlug($routeSlug);
        if ($route === null) {
            return Response::view('public/not-found', ['title' => 'Løype ikke funnet'], 404);
        }

        $stops = array_values(array_filter(
            $this->container->stopService()->listForRoute($route->id),
            static fn ($stop) => $stop->status === \App\Domain\Stop::STATUS_PUBLISHED
        ));

        return Response::view('public/route', [
            'title' => $route->name,
            'route' => $route,
            'stops' => $stops,
            'participant' => Session::getParticipant(),
            'flash' => Session::pullFlash(),
        ]);
    }

    /** @return array{status: int, headers: array<string, string>, body: string} */
    public function stop(string $routeSlug, string $stopSlug): array
    {
        $route = $this->container->routeService()->findPublishedBySlug($routeSlug);
        if ($route === null) {
            return Response::view('public/not-found', ['title' => 'Løype ikke funnet'], 404);
        }

        $stop = $this->container->stopService()->findPublishedBySlug($routeSlug, $stopSlug);
        if ($stop === null) {
            return Response::view('public/not-found', ['title' => 'Post ikke funnet'], 404);
        }

        $viaQr = isset($_GET['qr']) && (string) $_GET['qr'] === '1';

        return $this->renderStopPage($route, $stop, $viaQr);
    }

    /** @return array{status: int, headers: array<string, string>, body: string} */
    public function renderStopPage(\App\Domain\Route $route, \App\Domain\Stop $stop, bool $viaQr): array
    {
        $neighbours = $this->container->stopService()->neighbours($stop);
        $participant = Session::getParticipant();
        $existingAnswer = null;
        $guestAnswer = null;
        if ($participant !== null) {
            $existingAnswer = $this->container->answerService()->findForParticipantAndStop(
                $participant['id'],
                $stop->id
            );
        } else {
            $guestAnswer = Session::getGuestAnswer($stop->id);
        }

        return Response::view('public/stop', [
            'title' => $stop->title,
            'route' => $route,
            'stop' => $stop,
            'prev' => $viaQr ? null : $neighbours['prev'],
            'next' => $viaQr ? null : $neighbours['next'],
            'viaQr' => $viaQr,
            'participant' => $participant,
            'existingAnswer' => $existingAnswer,
            'guestAnswer' => $guestAnswer,
            'csrf' => Csrf::field(),
            'flash' => Session::pullFlash(),
        ]);
    }

    /** @return array{status: int, headers: array<string, string>, body: string} */
    public function leaderboard(string $routeSlug): array
    {
        $route = $this->container->routeService()->findPublishedBySlug($routeSlug);
        if ($route === null) {
            return Response::view('public/not-found', ['title' => 'Løype ikke funnet'], 404);
        }

        return Response::view('public/leaderboard', [
            'title' => 'Resultattavle — ' . $route->name,
            'route' => $route,
            'entries' => $this->container->leaderboardService()->forRoute($route->id),
            'participant' => Session::getParticipant(),
            'flash' => Session::pullFlash(),
        ]);
    }
}
