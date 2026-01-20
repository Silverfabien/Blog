<?php

namespace App\ControllerHandler\Security;

use App\Entity\User\User;
use App\Repository\User\UserRepository;
use Symfony\Component\Form\FormInterface;

readonly class UserControllerHandler
{
    public function __construct(private UserRepository $userRepository) {}

    public function new(FormInterface $form, User $user): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->create($user);

            return true;
        }

        return false;
    }
}
