<?php

namespace App\MessageHandler;

use App\Message\UserUpdated;
use App\Repository\User\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class UserUpdatedHandler
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function __invoke(UserUpdated $message): void
    {
        $user = $this->userRepository->findOneBy(['userApiId' => $message->id]);

        if (!$user) {
            return;
        }

        $user->setUsername($message->username);
        $user->setEmail($message->email);

        $this->userRepository->update($user);
    }
}
