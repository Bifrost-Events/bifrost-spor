<?php

declare(strict_types=1);

namespace App\Controller;

use App\Support\Container;
use App\Support\PrintLayout;
use App\Support\Response;
use App\Support\RouteOwnership;

final class PrintController
{
    public function __construct(
        private readonly Container $container,
    ) {
    }

    /** @return array{status: int, headers: array<string, string>, body: string} */
    public function stop(string $stopId): array
    {
        $stop = $this->container->stopRepository()->findById($stopId);
        if ($stop === null) {
            return Response::view('public/not-found', ['title' => 'Post ikke funnet'], 404);
        }

        $route = $this->container->routeService()->findById($stop->routeId);
        if ($route === null) {
            return Response::view('public/not-found', ['title' => 'Løype ikke funnet'], 404);
        }

        if ($redirect = RouteOwnership::requireOwner($route)) {
            return $redirect;
        }

        $sign = $this->container->printSignService()->buildSign($route, $stop);
        $perPage = PrintLayout::parsePerPage($_GET['per_page'] ?? null);

        return Response::view('admin/print/signs', [
            'title' => 'Postskilt — ' . $stop->title,
            'signs' => [$sign],
            'perPage' => $perPage,
            'showLayoutSelector' => false,
        ]);
    }

    /** @return array{status: int, headers: array<string, string>, body: string} */
    public function route(string $routeId): array
    {
        $route = $this->container->routeService()->findById($routeId);
        if ($route === null) {
            return Response::view('public/not-found', ['title' => 'Løype ikke funnet'], 404);
        }

        if ($redirect = RouteOwnership::requireOwner($route)) {
            return $redirect;
        }

        $stops = $this->container->stopService()->listForRoute($route->id);
        $signs = $this->container->printSignService()->buildSignsForRoute($route, $stops);
        $perPage = PrintLayout::parsePerPage($_GET['per_page'] ?? null);

        return Response::view('admin/print/signs', [
            'title' => 'Postskilt — ' . $route->name,
            'signs' => $signs,
            'perPage' => $perPage,
            'showLayoutSelector' => true,
        ]);
    }
}
