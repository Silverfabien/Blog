<?php

namespace App\Controller\Security;

use App\ControllerHandler\SecurityHandler;
use App\Entity\User\User;
use App\Form\Security\LoginType;
use App\Form\Security\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class SecurityController extends AbstractController
{
    public function __construct(
        private readonly SecurityHandler $securityHandler
    ) {}

    private function isConnected(Request $request): ?Response
    {
        $jwt = $request->cookies->get('jwt_token');

        if ($jwt) {
            return $this->redirectToRoute('default');
        }

        return null;
    }

    #[Route('/login', name: 'login')]
    public function login(Request $request): Response
    {
        $redirect = $this->isConnected($request);

        if ($redirect !== null) {
            return $redirect;
        }

        $form = $this->createForm(LoginType::class);

        return $this->render('security/login.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request): Response
    {
        $redirect = $this->isConnected($request);

        if ($redirect !== null) {
            return $redirect;
        }

        $form = $this->createForm(RegisterType::class);

//        $user = new User();
//        if ($this->securityHandler->createUser($user)) {
//            return $this->redirectToRoute('default');
//        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(Request $request): Response
    {
        $redirect = $this->isConnected($request);

        if ($redirect !== null) {
            return $this->render('security/logout.html.twig');
        }

        return $this->redirectToRoute('login');
    }

    #[Route('/logout-session', name: 'logout_session', methods: ['POST'])]
    public function logoutSession(SessionInterface $session): Response
    {
        $session->invalidate();

        return new Response('Session détruite.', Response::HTTP_OK);
    }
}
