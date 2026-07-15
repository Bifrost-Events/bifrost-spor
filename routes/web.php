<?php

declare(strict_types=1);

use App\Controller\AdminRouteController;
use App\Controller\AdminStopController;
use App\Controller\AnswerController;
use App\Controller\HealthController;
use App\Controller\ParticipantAuthController;
use App\Controller\PrintController;
use App\Controller\PublicController;
use App\Controller\QrRedirectController;
use App\Support\Response;
use App\Support\Router;

return function (array $app): Router {
    $container = $app['container'];
    $router = new Router();

    $public = new PublicController($container);
    $qr = new QrRedirectController($container);
    $participantAuth = new ParticipantAuthController($container);
    $answers = new AnswerController($container);
    $adminRoutes = new AdminRouteController($container);
    $adminStops = new AdminStopController($container);
    $print = new PrintController($container);

    $router->get('/health', fn () => (new HealthController())());
    $router->get('/', fn () => $public->home());
    $router->get('/spor/{routeSlug}', fn (string $routeSlug) => $public->route($routeSlug));
    $router->get('/spor/{routeSlug}/resultater', fn (string $routeSlug) => $public->leaderboard($routeSlug));
    $router->get('/spor/{routeSlug}/{stopSlug}', fn (string $routeSlug, string $stopSlug) => $public->stop($routeSlug, $stopSlug));
    $router->post('/spor/{routeSlug}/{stopSlug}/svar', fn (string $routeSlug, string $stopSlug) => $answers->submit($routeSlug, $stopSlug));
    $router->get('/q/{token}', fn (string $token) => $qr->show($token));

    $router->get('/registrer', fn () => $participantAuth->showRegister());
    $router->post('/registrer', fn () => $participantAuth->register());
    $router->get('/logg-inn', fn () => $participantAuth->showLogin());
    $router->post('/logg-inn', fn () => $participantAuth->login());
    $router->get('/logg-ut', fn () => $participantAuth->logout());

    $router->get('/admin/login', fn () => Response::redirect('/logg-inn?redirect=' . rawurlencode('/admin/routes')));
    $router->get('/admin/logout', fn () => Response::redirect('/logg-ut'));

    $router->get('/admin', fn () => $adminRoutes->index());
    $router->get('/admin/routes', fn () => $adminRoutes->index());
    $router->get('/admin/routes/create', fn () => $adminRoutes->createForm());
    $router->post('/admin/routes', fn () => $adminRoutes->create());
    $router->get('/admin/routes/{routeId}', fn (string $routeId) => $adminRoutes->show($routeId));
    $router->get('/admin/routes/{routeId}/stops/create', fn (string $routeId) => $adminStops->createForm($routeId));
    $router->post('/admin/routes/{routeId}/stops', fn (string $routeId) => $adminStops->create($routeId));
    $router->get('/admin/routes/{routeId}/print', fn (string $routeId) => $print->route($routeId));
    $router->get('/admin/stops/{stopId}/print', fn (string $stopId) => $print->stop($stopId));

    return $router;
};
