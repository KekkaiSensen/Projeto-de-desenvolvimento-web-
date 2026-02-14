<?php

namespace Services;

use Predis\Client;

class CacheService
{
    private $client;
    private $enabled = false;

    public function __construct()
    {
        try {
            $this->client = new Client([
                'scheme' => 'tcp',
                'host'   => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
                'port'   => $_ENV['REDIS_PORT'] ?? 6379,
                'password' => $_ENV['REDIS_PASSWORD'] ?? null,
                'timeout' => 2.0, // Fail fast
            ]);
            $this->client->connect();
            $this->enabled = true;
        } catch (\Exception $e) {
            // Redis unavailable, fallback to no-cache
            $this->enabled = false;
            // Optionally log error here: error_log("Redis connection failed: " . $e->getMessage());
        }
    }

    public function get($key)
    {
        if (!$this->enabled) {
            return null;
        }
        try {
            $value = $this->client->get($key);
            return $value ? json_decode($value, true) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function set($key, $value, $ttl = 300)
    {
        if (!$this->enabled) {
            return;
        }
        try {
            // TTL default 5 minutes
            $this->client->setex($key, $ttl, json_encode($value));
        } catch (\Exception $e) {
            // Ignore write failures
        }
    }

    public function remember($key, $ttl, callable $callback)
    {
        $value = $this->get($key);

        if ($value !== null) {
            return $value;
        }

        $value = $callback();

        if ($value !== null) {
            $this->set($key, $value, $ttl);
        }

        return $value;
    }

    public function forget($key)
    {
        if (!$this->enabled) {
            return false;
        }
        try {
            return $this->client->del([$key]);
        } catch (\Exception $e) {
            return false;
        }
    }
}
