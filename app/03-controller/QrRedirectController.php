<?php

declare(strict_types=1);

namespace App\Controller;

use App\Support\Container;
use App\Support\Response;

final class QrRedirectController
{
    public function __construct(
        private readonly Container $container,
    ) {
    }

    /** @return array{status: int, headers: array<string, string>, body: string} */
    public function show(string $token): array
    {
        $stop = $this->container->stopService()->findByQrToken($token);
        if ($stop === null || $stop->status !== \App\Domain\Stop::STATUS_PUBLISHED) {
            return Response::view('public/not-found', ['title' => 'Ukjent QR-kode'], 404);
        }

        $route = $this->container->routeService()->findById($stop->routeId);
        if ($route === null || $route->status !== \App\Domain\Route::STATUS_PUBLISHED) {
            return Response::view('public/not-found', ['title' => 'Løype ikke funnet'], 404);
        }

        return (new PublicController($this->container))->renderStopPage($route, $stop, true);
    }
}
