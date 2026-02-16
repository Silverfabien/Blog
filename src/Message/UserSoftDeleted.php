<?php

namespace App\Message;

class UserSoftDeleted
{
    public function __construct(
        public int $id,
        public string $username,
        public string $email
    ) {}
}
