<?php

namespace App\MessageHandler;

use App\Entity\User\User;
use App\Message\UserCreated;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UserCreatedHandler
{
    public function __construct(private EntityManagerInterface $em) {}

    public function __invoke(UserCreated $message): void
    {
        $user = new User();
        $user->setUserApiId($message->id);
        $user->setUsername($message->username);
        $user->setEmail($message->email);
        $user->setRole($message->role);

        $this->em->persist($message);
        $this->em->flush();
    }
}
