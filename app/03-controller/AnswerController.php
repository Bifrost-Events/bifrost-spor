<?php

declare(strict_types=1);

namespace App\Controller;

use App\Support\AppLogger;
use App\Support\Container;
use App\Support\Csrf;
use App\Support\Response;
use App\Support\Session;
use App\Support\ValidationException;

final class AnswerController
{
    public function __construct(
        private readonly Container $container,
    ) {
    }

    /** @return array{status: int, headers: array<string, string>, body: string} */
    public function submit(string $routeSlug, string $stopSlug): array
    {
        $viaQr = isset($_POST['via_qr']) && (string) $_POST['via_qr'] === '1';
        $redirectUrl = '/spor/' . rawurlencode($routeSlug) . '/' . rawurlencode($stopSlug);
        if ($viaQr) {
            $redirectUrl .= '?qr=1';
        }

        $route = $this->container->routeService()->findPublishedBySlug($routeSlug);
        $stop = $this->container->stopService()->findPublishedBySlug($routeSlug, $stopSlug);
        if ($route === null || $stop === null) {
            return Response::view('public/not-found', ['title' => 'Post ikke funnet'], 404);
        }

        try {
            Csrf::validateRequest();
            $selectedOptionId = (string) ($_POST['option_id'] ?? '');
            $participant = Session::getParticipant();

            if ($participant === null) {
                $isCorrect = $this->container->answerService()->evaluate($stop, $selectedOptionId);
                Session::setGuestAnswer($stop->id, $selectedOptionId, $isCorrect);

                $message = $isCorrect
                    ? 'Riktig svar! Logg inn for å registrere svaret på resultattavlen.'
                    : 'Feil svar denne gangen. Logg inn for å registrere svaret ditt i konkurransen.';
                Session::setFlash($isCorrect ? 'success' : 'error', $message);
            } else {
                $answer = $this->container->answerService()->submit(
                    $participant['id'],
                    $stop,
                    $selectedOptionId,
                );

                $message = $answer->isCorrect
                    ? 'Riktig svar! Svaret er registrert.'
                    : 'Feil svar denne gangen. Svaret er registrert — fortsett løypa og prøv igjen på neste post.';
                Session::setFlash($answer->isCorrect ? 'success' : 'error', $message);
            }
        } catch (ValidationException $e) {
            Session::setFlash('error', $e->getMessage());
        } catch (\Throwable $e) {
            AppLogger::error('Kunne ikke lagre svar', ['message' => $e->getMessage()]);
            Session::setFlash('error', 'Kunne ikke behandle svaret. Prøv igjen.');
        }

        return Response::redirect($redirectUrl);
    }
}
