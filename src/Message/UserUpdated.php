<?php

namespace App\Message;

class UserUpdated
{
    public function __construct(
        public int $id,
        public string $username,
        public string $email
    ) {}
}
