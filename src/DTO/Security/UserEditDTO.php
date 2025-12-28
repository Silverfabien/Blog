<?php

namespace App\DTO\Security;

use Symfony\Component\Validator\Constraints as Assert;

class UserEditDTO
{
    #[Assert\NotBlank]
    public ?string $username = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    public ?string $firstname = null;

    public ?string $lastname = null;

    public static function fromJwtData(array $userInfo): self
    {
        $dto = new self();

        $dto->username = $userInfo['user']['username'];
        $dto->email = $userInfo['user']['email'];

        $dto->firstname = $userInfo['otherData']['firstname'] ?? null;
        $dto->lastname = $userInfo['otherData']['lastname'] ?? null;

        return $dto;
    }
}
