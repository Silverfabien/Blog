<?php

namespace App\Message\Admin;

class UserUpdated
{
    public function __construct(
        public int $id,
        public string $username,
        public string $email,
        public string $role
    ) {}
}
