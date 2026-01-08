<?php

namespace App\Controller\Security;

use App\Form\Security\LoginType;
use App\Form\Security\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class SecurityController extends AbstractController
{
    private function isConnected(Request $request): ?Response
    {
        $jwt = $request->cookies->get('jwt_token');

        if ($jwt) {
            return $this->redirectToRoute('default');
        }

        return null;
    }

    #[Route('/auth-modals', name: 'auth_modals')]
    public function authModals(): Response
    {
        $loginForm = $this->createForm(LoginType::class);
        $registerForm = $this->createForm(RegisterType::class);

        return $this->render('security/_auth_modals.html.twig', [
            'loginForm' => $loginForm->createView(),
            'registerForm' => $registerForm->createView(),
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(Request $request): Response
    {
        $redirect = $this->isConnected($request);

        if ($redirect !== null) {
            return $this->render('security/logout.html.twig');
        }

        return $this->redirectToRoute('default');
    }

    #[Route('/logout-session', name: 'logout_session', methods: ['POST'])]
    public function logoutSession(SessionInterface $session): Response
    {
        $session->invalidate();

        return new Response('Session détruite.', Response::HTTP_OK);
    }
}
