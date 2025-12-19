<?php

namespace App\Message;

class UserCreated
{
    public function __construct(
        public int $id,
        public string $username,
        public string $email
    ) {}
}
