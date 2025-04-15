<?php

declare(strict_types=1);

namespace App\Service;

use Predis\ClientInterface;

class RedisService
{
    private ClientInterface $redisClient;

    public function __construct(ClientInterface $redisClient)
    {
        $this->redisClient = $redisClient;
    }

    public function set(string $key, mixed $value, ?int $ttl = null): void
    {
        $serialized = serialize($value);
        $this->redisClient->set($key, $serialized);

        if ($ttl !== null) {
            $this->redisClient->expire($key, $ttl);
        }
    }

    public function get(string $key): mixed
    {
        $value = $this->redisClient->get($key);

        return $value !== null ? unserialize($value) : null;
    }

    public function delete(string $key): void
    {
        $this->redisClient->del([$key]);
    }

    public function has(string $key): bool
    {
        return $this->redisClient->exists($key) > 0;
    }

    public function expire(string $key, int $ttl): bool
    {
        return (bool) $this->redisClient->expire($key, $ttl);
    }
}
