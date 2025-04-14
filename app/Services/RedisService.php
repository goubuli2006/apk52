<?php

namespace App\Services;

class RedisService
{
    private static $instance;
    
    public static function get(): \Redis
    {
        if (!self::$instance) {
            $redisConfig = self::getRedisConfig();
            self::$instance = self::connect($redisConfig);
        }
        return self::$instance;
    }

    private static function connect(array $redisConfig)
    {
        $redis = new \Redis();
        $redis->connect($redisConfig['host'], $redisConfig['port'], $redisConfig['timeout']);
        $redis->auth($redisConfig['password']);
        $redis->select($redisConfig['db_number']);
        return $redis;
    }

    private static function getRedisConfig(): array
    {
        $redisConfig['host'] = env('redis.hostname');
        $redisConfig['password'] = env('redis.password');
        $redisConfig['port'] = env('redis.port');
        $redisConfig['timeout']   = env('redis.timeout', 600);
        $redisConfig['db_number'] = env('redis.db_number', 0);
        return $redisConfig;
    }
}