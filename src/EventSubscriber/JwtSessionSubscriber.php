<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class JwtSessionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private string $siteName
    ) {}

    public function onKernelRequest(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return;
        }

        $session = $request->getSession();
        $jwtToken = $request->cookies->get('jwt_token');

        if ($jwtToken && !$session->has('id')) {
            $payload = json_decode(base64_decode(explode(".", $jwtToken)[1]), true);

            if ($payload) {
                $session->set('id', $payload['id']);
                $session->set('username', $payload['username']);
                $session->set('email', $payload['email']);

                $role = $payload['role'] ?? null;

                if (is_array($role)) {
                    $role = $role[$this->siteName] ?? $role[strtolower($this->siteName)] ?? null;
                } elseif (!is_string($role) || $role === '') {
                    $role = 'ROLE_USER';
                }

                $session->set('role', $role);
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return ['kernel.request' => 'onKernelRequest'];
    }
}
