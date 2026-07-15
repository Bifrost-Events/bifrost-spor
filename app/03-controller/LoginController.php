<?php

declare(strict_types=1);

namespace App\Controller;

use App\Support\Auth;
use App\Support\Container;
use App\Support\Csrf;
use App\Support\Response;
use App\Support\Session;
use App\Support\ValidationException;

final class LoginController
{
    public function __construct(
        private readonly Container $container,
    ) {
    }

    /** @return array{status: int, headers: array<string, string>, body: string} */
    public function showForm(): array
    {
        if (Auth::check()) {
            return Response::redirect('/admin/routes');
        }

        return Response::view('admin/login', [
            'title' => 'Admininnlogging',
            'error' => '',
            'csrf' => Csrf::field(),
            'flash' => Session::pullFlash(),
        ]);
    }

    /** @return array{status: int, headers: array<string, string>, body: string} */
    public function submit(): array
    {
        try {
            Csrf::validateRequest();
        } catch (ValidationException $e) {
            return Response::view('admin/login', [
                'title' => 'Admininnlogging',
                'error' => $e->getMessage(),
                'csrf' => Csrf::field(),
            ], 400);
        }

        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $result = $this->container->authService()->login($username, $password);

        if (!$result['ok']) {
            return Response::view('admin/login', [
                'title' => 'Admininnlogging',
                'error' => (string) ($result['error'] ?? 'Innlogging feilet.'),
                'csrf' => Csrf::field(),
            ], 401);
        }

        return Response::redirect('/admin/routes');
    }

    /** @return array{status: int, headers: array<string, string>, body: string} */
    public function logout(): array
    {
        $this->container->authService()->logout();
        Session::setFlash('success', 'Du er nå utlogget.');

        return Response::redirect('/admin/login');
    }
}
