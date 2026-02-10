<?php

namespace App\ControllerHandler\Admin\Security;

use App\Repository\User\UserRepository;

class UserControllerHandler
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function edit(): bool
    {
        $this->userRepository->update();

        return true;
    }
}
