<?php

namespace App\Controller\Admin\Security;

use App\DTO\Admin\Security\UserEditDTO;
use App\Entity\User\User;
use App\Form\Admin\Security\UserEditType;
use App\Repository\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/user', name: 'admin_user_')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    #[Route('/', name: 'index')]
    public function index(
        SessionInterface $session
    ): Response
    {
        $user = $this->userRepository->findOneBy(['userApiId' => $session->get('id')]);
        $allUsers = $this->userRepository->findAll();

        return $this->render('admin/security/index.html.twig', [
            'user' => $user,
            'allUsers' => $allUsers
        ]);
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(
        Request $request,
        User $user
    ): Response
    {
        $userDto = UserEditDTO::fromUser($user);

        $userForm = $this->createForm(UserEditType::class, $userDto)->handleRequest($request);

        return $this->render('admin/security/edit.html.twig', [
            'user' => $user,
            'userForm' => $userForm->createView()
        ]);
    }
}
