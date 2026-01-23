<?php

namespace App\EventListener;

use App\Service\MaintenanceChecker;
use Silversat\PermissionBundle\Security\PermissionChecker;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\Response;

readonly class MaintenanceListener
{
    public function __construct(
        private MaintenanceChecker $maintenance,
        private PermissionChecker $permissionChecker
    ) {}

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $jwt = $event->getRequest()->cookies->get('jwt_token');

        if (!$jwt) {
            $this->handleMaintenance($event);
            return;
        }

        $jwtDecode = json_decode(base64_decode(explode(".", $jwt)[1]), true);

        // Admin connecté → bypass
        if ($this->permissionChecker->isPermissionGranted($jwtDecode, "ROLE_ADMIN")) {
            return;
        }

        $this->handleMaintenance($event);
    }

    private function handleMaintenance(RequestEvent $event): void
    {
        $siteKey = 'blog';

        if ($this->maintenance->isEnabled($siteKey)) {
            $response = new Response(
                file_get_contents(__DIR__ . '/../../public/maintenance/maintenance.html'),
                503
            );

            $response->headers->set('Retry-After', 3600);
            $event->setResponse($response);
        }
    }
}
