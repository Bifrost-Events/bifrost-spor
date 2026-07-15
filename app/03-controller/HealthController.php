<?php

declare(strict_types=1);

namespace App\Controller;

use App\Support\Response;

final class HealthController
{
    public function __invoke(): array
    {
        return Response::json([
            'spor' => 'ok',
            'status' => 'ok',
        ]);
    }
}
