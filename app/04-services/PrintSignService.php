<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Route;
use App\Domain\Stop;
use App\Support\AppUrl;
use App\Support\QrCodeSvgRenderer;

final class PrintSignService
{
    /**
     * @return array{
     *     route_name: string,
     *     stop_title: string,
     *     position: int,
     *     qr_token: string,
     *     scan_url: string,
     *     qr_svg: string
     * }
     */
    public function buildSign(Route $route, Stop $stop): array
    {
        $scanUrl = AppUrl::qrScanUrl($stop->qrToken);

        return [
            'route_name' => $route->name,
            'stop_title' => $stop->title,
            'position' => $stop->position,
            'qr_token' => $stop->qrToken,
            'scan_url' => $scanUrl,
            'qr_svg' => QrCodeSvgRenderer::render($scanUrl),
        ];
    }

    /**
     * @param Stop[] $stops
     * @return list<array{
     *     route_name: string,
     *     stop_title: string,
     *     position: int,
     *     qr_token: string,
     *     scan_url: string,
     *     qr_svg: string
     * }>
     */
    public function buildSignsForRoute(Route $route, array $stops): array
    {
        $signs = [];
        foreach ($stops as $stop) {
            $signs[] = $this->buildSign($route, $stop);
        }

        return $signs;
    }
}
