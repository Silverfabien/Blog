<?php

namespace App\ControllerHandler;

use App\Entity\User\User;
use App\Repository\User\UserRepository;

readonly class SecurityHandler
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function createUser(User $user): bool
    {
        $this->userRepository->create($user);

        return true;
    }
}
