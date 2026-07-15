<?php

declare(strict_types=1);

namespace App\Controller;

use App\Support\Container;
use App\Support\Csrf;
use App\Support\Response;
use App\Support\Session;
use App\Support\ValidationException;

final class ParticipantAuthController
{
    public function __construct(
        private readonly Container $container,
    ) {
    }

    /** @return array{status: int, headers: array<string, string>, body: string} */
    public function showRegister(): array
    {
        if (Session::isParticipantLoggedIn()) {
            return Response::redirect('/');
        }

        return Response::view('public/register', [
            'title' => 'Registrer deg',
            'error' => '',
            'old' => [],
            'redirect' => $this->safeRedirect((string) ($_GET['redirect'] ?? '/')),
            'csrf' => Csrf::field(),
        ]);
    }

    /** @return array{status: int, headers: array<string, string>, body: string} */
    public function register(): array
    {
        try {
            Csrf::validateRequest();
        } catch (ValidationException $e) {
            return Response::view('public/register', [
                'title' => 'Registrer deg',
                'error' => $e->getMessage(),
                'old' => $_POST,
                'redirect' => $this->safeRedirect((string) ($_POST['redirect'] ?? '/')),
                'csrf' => Csrf::field(),
            ], 400);
        }

        $result = $this->container->participantAuthService()->register(
            (string) ($_POST['name'] ?? ''),
            (string) ($_POST['email'] ?? ''),
            (string) ($_POST['password'] ?? ''),
            (string) ($_POST['password_confirm'] ?? ''),
        );

        if (!$result['ok']) {
            return Response::view('public/register', [
                'title' => 'Registrer deg',
                'error' => (string) ($result['error'] ?? 'Registrering feilet.'),
                'old' => $_POST,
                'redirect' => $this->safeRedirect((string) ($_POST['redirect'] ?? '/')),
                'csrf' => Csrf::field(),
            ], 422);
        }

        Session::setFlash('success', $this->authSuccessMessage(
            'Velkommen! Du er registrert og kan nå svare på spørsmål i konkurransen.',
            $this->persistGuestAnswersAfterAuth()
        ));

        return Response::redirect($this->safeRedirect((string) ($_POST['redirect'] ?? '/')));
    }

    /** @return array{status: int, headers: array<string, string>, body: string} */
    public function showLogin(): array
    {
        if (Session::isParticipantLoggedIn()) {
            return Response::redirect('/');
        }

        return Response::view('public/login', [
            'title' => 'Logg inn',
            'error' => '',
            'redirect' => (string) ($_GET['redirect'] ?? '/'),
            'csrf' => Csrf::field(),
        ]);
    }

    /** @return array{status: int, headers: array<string, string>, body: string} */
    public function login(): array
    {
        $redirect = (string) ($_POST['redirect'] ?? '/');
        if ($redirect === '' || !str_starts_with($redirect, '/')) {
            $redirect = '/';
        }

        try {
            Csrf::validateRequest();
        } catch (ValidationException $e) {
            return Response::view('public/login', [
                'title' => 'Logg inn',
                'error' => $e->getMessage(),
                'redirect' => $redirect,
                'csrf' => Csrf::field(),
            ], 400);
        }

        $result = $this->container->participantAuthService()->login(
            (string) ($_POST['email'] ?? ''),
            (string) ($_POST['password'] ?? ''),
        );

        if (!$result['ok']) {
            return Response::view('public/login', [
                'title' => 'Logg inn',
                'error' => (string) ($result['error'] ?? 'Innlogging feilet.'),
                'redirect' => $redirect,
                'csrf' => Csrf::field(),
            ], 401);
        }

        Session::setFlash('success', $this->authSuccessMessage(
            'Velkommen tilbake!',
            $this->persistGuestAnswersAfterAuth()
        ));

        return Response::redirect($redirect);
    }

    /** @return array{status: int, headers: array<string, string>, body: string} */
    public function logout(): array
    {
        $this->container->participantAuthService()->logout();
        Session::setFlash('success', 'Du er nå utlogget.');

        return Response::redirect('/');
    }

    private function safeRedirect(string $redirect): string
    {
        if ($redirect === '' || !str_starts_with($redirect, '/')) {
            return '/';
        }

        return $redirect;
    }

    private function persistGuestAnswersAfterAuth(): int
    {
        $participant = Session::getParticipant();
        if ($participant === null) {
            return 0;
        }

        return $this->container->answerService()->persistGuestAnswers($participant['id']);
    }

    private function authSuccessMessage(string $baseMessage, int $savedGuestAnswers): string
    {
        if ($savedGuestAnswers <= 0) {
            return $baseMessage;
        }

        $suffix = $savedGuestAnswers === 1
            ? ' Ditt svar er nå registrert i konkurransen.'
            : ' ' . $savedGuestAnswers . ' svar er nå registrert i konkurransen.';

        return rtrim($baseMessage, '.') . '.' . $suffix;
    }
}
