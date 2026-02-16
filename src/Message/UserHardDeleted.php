<?php

namespace App\Message;

class UserHardDeleted
{
    public function __construct(
        public int $id
    ) {}
}
