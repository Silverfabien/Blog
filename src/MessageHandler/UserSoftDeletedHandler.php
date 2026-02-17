<?php

namespace App\MessageHandler;

use App\Message\UserSoftDeleted;
use App\Repository\User\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class UserSoftDeletedHandler
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function __invoke(UserSoftDeleted $message): void
    {
        $user = $this->userRepository->findOneBy(['userApiId' => $message->id]);

        if (!$user) {
            return;
        }

        $user->setUsername($message->username);
        $user->setEmail($message->email);
        $user->setSignature(null);

        $this->userRepository->update($user);
    }
}
