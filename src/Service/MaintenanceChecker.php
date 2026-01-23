<?php

namespace App\Service;


readonly class MaintenanceChecker
{
    public function __construct(
        private RedisClient $redis
    ) {}

    public function isEnabled(string $siteKey): bool
    {
        $siteValue = $this->redis->get('maintenance_site_' . $siteKey);

        if ($siteValue === '1') {
            return true;
        }

        if ($siteValue === '0') {
            return false;
        }

        return $this->redis->get('maintenance_global');
    }
}
