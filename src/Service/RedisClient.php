<?php

namespace App\Service;

use AllowDynamicProperties;
use Predis\Client;

#[AllowDynamicProperties]
class RedisClient
{
    public function __construct(string $dsn)
    {
        $this->client = new Client($dsn);
    }

    public function get(string $key): ?string
    {
        return $this->client->get($key);
    }

    public function set(string $key, string $value): void
    {
        $this->client->set($key, $value);
    }

    public function del(string $key): void
    {
        $this->client->del([$key]);
    }
}
