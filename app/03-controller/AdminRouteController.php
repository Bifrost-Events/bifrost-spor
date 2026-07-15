<?php

declare(strict_types=1);

namespace App\Controller;

use App\Support\AppLogger;
use App\Support\Config;
use App\Support\Container;
use App\Support\Csrf;
use App\Support\Response;
use App\Support\RouteOwnership;
use App\Support\Session;
use App\Support\ValidationException;

final class AdminRouteController
{
    public function __construct(
        private readonly Container $container,
    ) {
    }

    /** @return array{status: int, headers: array<string, string>, body: string} */
    public function index(): array
    {
        if ($redirect = RouteOwnership::requireParticipant('/admin/routes')) {
            return $redirect;
        }

        $participant = Session::getParticipant();
        $routes = $this->container->routeService()->listForOwner($participant['id'] ?? '');
        $stopCounts = [];
        foreach ($routes as $route) {
            $stopCounts[$route->id] = count($this->container->stopService()->listForRoute($route->id));
        }

        return Response::view('admin/routes/index', [
            'title' => 'Mine løyper',
            'routes' => $routes,
            'stopCounts' => $stopCounts,
            'participant' => $participant,
            'flash' => Session::pullFlash(),
        ]);
    }

    /** @return array{status: int, headers: array<string, string>, body: string} */
    public function createForm(): array
    {
        if ($redirect = RouteOwnership::requireParticipant('/admin/routes/create')) {
            return $redirect;
        }

        return Response::view('admin/routes/create', [
            'title' => 'Opprett løype',
            'themes' => Config::get('app.themes', []),
            'error' => '',
            'old' => [],
            'participant' => Session::getParticipant(),
            'csrf' => Csrf::field(),
        ]);
    }

    /** @return array{status: int, headers: array<string, string>, body: string} */
    public function create(): array
    {
        if ($redirect = RouteOwnership::requireParticipant('/admin/routes/create')) {
            return $redirect;
        }

        $participant = Session::getParticipant();

        try {
            Csrf::validateRequest();
            $route = $this->container->routeService()->create([
                'owner_id' => $participant['id'] ?? '',
                'name' => (string) ($_POST['name'] ?? ''),
                'slug' => (string) ($_POST['slug'] ?? ''),
                'description' => (string) ($_POST['description'] ?? ''),
                'status' => (string) ($_POST['status'] ?? 'draft'),
                'theme' => (string) ($_POST['theme'] ?? 'default'),
            ]);
            Session::setFlash('success', 'Løypen «' . $route->name . '» ble opprettet.');

            return Response::redirect('/admin/routes/' . rawurlencode($route->id));
        } catch (ValidationException $e) {
            return Response::view('admin/routes/create', [
                'title' => 'Opprett løype',
                'themes' => Config::get('app.themes', []),
                'error' => $e->getMessage(),
                'old' => $_POST,
                'participant' => $participant,
                'csrf' => Csrf::field(),
            ], 422);
        } catch (\Throwable $e) {
            AppLogger::error('Kunne ikke opprette løype', ['message' => $e->getMessage()]);

            return Response::view('admin/routes/create', [
                'title' => 'Opprett løype',
                'themes' => Config::get('app.themes', []),
                'error' => 'Kunne ikke lagre løypen. Prøv igjen.',
                'old' => $_POST,
                'participant' => $participant,
                'csrf' => Csrf::field(),
            ], 500);
        }
    }

    /** @return array{status: int, headers: array<string, string>, body: string} */
    public function show(string $routeId): array
    {
        if ($redirect = RouteOwnership::requireParticipant('/admin/routes/' . rawurlencode($routeId))) {
            return $redirect;
        }

        $route = $this->container->routeService()->findById($routeId);
        if ($route === null) {
            return Response::view('public/not-found', ['title' => 'Løype ikke funnet'], 404);
        }

        if ($redirect = RouteOwnership::requireOwner($route)) {
            return $redirect;
        }

        $stops = $this->container->stopService()->listForRoute($route->id);

        return Response::view('admin/routes/show', [
            'title' => $route->name,
            'route' => $route,
            'stops' => $stops,
            'participant' => Session::getParticipant(),
            'flash' => Session::pullFlash(),
            'appUrl' => rtrim((string) ($_ENV['APP_URL'] ?? ''), '/'),
        ]);
    }
}
