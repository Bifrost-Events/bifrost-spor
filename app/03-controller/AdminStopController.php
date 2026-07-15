<?php

declare(strict_types=1);

namespace App\Controller;

use App\Support\AppLogger;
use App\Support\Container;
use App\Support\Csrf;
use App\Support\Response;
use App\Support\RouteOwnership;
use App\Support\Session;
use App\Support\ValidationException;

final class AdminStopController
{
    public function __construct(
        private readonly Container $container,
    ) {
    }

    /** @return array{status: int, headers: array<string, string>, body: string} */
    public function createForm(string $routeId): array
    {
        if ($redirect = RouteOwnership::requireParticipant('/admin/routes/' . rawurlencode($routeId) . '/stops/create')) {
            return $redirect;
        }

        $route = $this->container->routeService()->findById($routeId);
        if ($route === null) {
            return Response::view('public/not-found', ['title' => 'Løype ikke funnet'], 404);
        }

        if ($redirect = RouteOwnership::requireOwner($route)) {
            return $redirect;
        }

        $nextPosition = count($this->container->stopService()->listForRoute($routeId)) + 1;

        return Response::view('admin/routes/stops/create', [
            'title' => 'Opprett post',
            'route' => $route,
            'participant' => Session::getParticipant(),
            'error' => '',
            'old' => ['position' => (string) $nextPosition],
            'csrf' => Csrf::field(),
        ]);
    }

    /** @return array{status: int, headers: array<string, string>, body: string} */
    public function create(string $routeId): array
    {
        if ($redirect = RouteOwnership::requireParticipant('/admin/routes/' . rawurlencode($routeId) . '/stops/create')) {
            return $redirect;
        }

        $route = $this->container->routeService()->findById($routeId);
        if ($route === null) {
            return Response::view('public/not-found', ['title' => 'Løype ikke funnet'], 404);
        }

        if ($redirect = RouteOwnership::requireOwner($route)) {
            return $redirect;
        }

        try {
            Csrf::validateRequest();
            $stop = $this->container->stopService()->create($routeId, [
                'title' => (string) ($_POST['title'] ?? ''),
                'slug' => (string) ($_POST['slug'] ?? ''),
                'body' => (string) ($_POST['body'] ?? ''),
                'question_text' => (string) ($_POST['question_text'] ?? ''),
                'option_1' => (string) ($_POST['option_1'] ?? ''),
                'option_2' => (string) ($_POST['option_2'] ?? ''),
                'option_3' => (string) ($_POST['option_3'] ?? ''),
                'option_4' => (string) ($_POST['option_4'] ?? ''),
                'correct_option' => (string) ($_POST['correct_option'] ?? '1'),
                'position' => (string) ($_POST['position'] ?? '1'),
                'status' => (string) ($_POST['status'] ?? 'draft'),
            ]);

            $publicUrl = '/q/' . rawurlencode($stop->qrToken);
            Session::setFlash(
                'success',
                'Posten «' . $stop->title . '» ble opprettet. QR-token: ' . $stop->qrToken . ' — URL: ' . $publicUrl
            );

            return Response::redirect('/admin/routes/' . rawurlencode($routeId));
        } catch (ValidationException $e) {
            return Response::view('admin/routes/stops/create', [
                'title' => 'Opprett post',
                'route' => $route,
                'participant' => Session::getParticipant(),
                'error' => $e->getMessage(),
                'old' => $_POST,
                'csrf' => Csrf::field(),
            ], 422);
        } catch (\Throwable $e) {
            AppLogger::error('Kunne ikke opprette post', ['message' => $e->getMessage()]);

            return Response::view('admin/routes/stops/create', [
                'title' => 'Opprett post',
                'route' => $route,
                'participant' => Session::getParticipant(),
                'error' => 'Kunne ikke lagre posten. Prøv igjen.',
                'old' => $_POST,
                'csrf' => Csrf::field(),
            ], 500);
        }
    }
}
