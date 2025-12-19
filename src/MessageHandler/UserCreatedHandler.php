<?php

namespace App\MessageHandler;

use App\Entity\User\User;
use App\Message\UserCreated;
use App\Repository\User\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class UserCreatedHandler
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function __invoke(UserCreated $message): void
    {
        if ($this->userRepository->findOneBy(['userApiId' => $message->id])) {
            return;
        }

        $user = new User();
        $user->setUserApiId($message->id);
        $user->setUsername($message->username);
        $user->setEmail($message->email);
        $user->setRole("ROLE_USER");

        $this->userRepository->create($user);
    }
}
