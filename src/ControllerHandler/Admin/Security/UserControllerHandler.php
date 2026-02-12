<?php

namespace App\ControllerHandler\Admin\Security;

use App\Entity\User\User;
use App\Repository\User\UserRepository;
use Symfony\Component\Form\FormInterface;

readonly class UserControllerHandler
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function editSignature(FormInterface $form, User $user): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->update($user);

            return true;
        }

        return false;
    }
}
