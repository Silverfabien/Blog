<?php

namespace App\MessageHandler;

use App\Message\UserHardDeleted;
use App\Repository\User\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class UserHardDeletedHandler
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function __invoke(UserHardDeleted $message): void
    {
        $user = $this->userRepository->findOneBy(['userApiId' => $message->id]);

        if (!$user) {
            return;
        }

        $this->userRepository->remove($user);
    }
}
