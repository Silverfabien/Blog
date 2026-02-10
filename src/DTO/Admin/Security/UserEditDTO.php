<?php

namespace App\DTO\Admin\Security;

use App\Entity\User\User;
use Symfony\Component\Validator\Constraints as Assert;

class UserEditDTO
{
    #[Assert\NotBlank]
    public ?string $username = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotBlank]
    public ?string $role = null;

    #[Assert\NotBlank]
    public ?bool $picture = null;

    public static function fromUser(User $user): self
    {
        $dto = new self();

        $dto->username = $user->getUsername();
        $dto->email = $user->getEmail();
        $dto->role = $user->getRole();
        $dto->picture = false;

        return $dto;
    }
}
