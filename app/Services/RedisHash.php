<?php

namespace App\Services;

use App\Helpers\RedisHelper;

class RedisHash extends RedisHelper
{
    public $fixPreKey = "";

    public function __construct($options = [])
    {
        $redisConfig = $options ? : self::getRedisConfig();
        parent::__construct($redisConfig);
    }

    private static function getRedisConfig(): array
    {
        $redisConfig['host']      = env('redis.hostname');
        $redisConfig['password']  = env('redis.password');
        $redisConfig['port']      = env('redis.port');
        $redisConfig['timeout']   = env('redis.timeout');
        $redisConfig['db_number'] = env('redis.db_number')?:0;
        return $redisConfig;
    }

    /**
     * 修复多站点时key名称
     *
     */
    public function setFixPreKey($fixPreKey = "")
    {
        $this->fixPreKey = $fixPreKey ?: (env('app.siteId') ? 'web_'.env('app.siteId').':' : 'web_'.md5(env('app.pc.domainUrl')).':');
        return $this;
    }

    public function getKeyToWriteGameViewRedis($redisPrefix, $id, $type)
    {
        $redisPrefix = $this->fixPreKey.$redisPrefix;
        $data = array('0' => 'mview', '1' => 'wview','2'=>'twview');
        $this->getKeyWriteToRedis($redisPrefix, $id, $type, $data);
    }

    private function getKeyWriteToRedis($redisPrefix, $id, $type, $data)
    {
        $time = date('YmdH0000');
        $currentTime = time();
        foreach ($data as $key => $val) {
            $redisKey = $redisPrefix . ":" . $type . ':' . $time . ':' . $val;

            $this->writeRedis($redisKey, $id, $type, $data);
        }
    }

    private function writeRedis($redisKey, $id, $type, $data)
    {
        $online = $this->setHashKey($redisKey);
        if (!$online->get((string)$id)) {
            $online->set((string)$id, '1');
        } else {
            $count = $online->get((string)$id);
            $count = $count + 1;
            $online->set((string)$id, (string)$count);
        }

        // 设置过期时间
        $online->setExpire(86400 * 10);
    }
    public function setRedisData($redisKey, $id, $data, $outTime)
    {
        $redisKey = $this->fixPreKey . $redisKey;
        $online = $this->setHashKey($redisKey);
        if (!$online->get((string)$id)) {
            $online->set((string)$id, json_encode($data));
            // 设置过期时间
            $online->setExpire($outTime);
        } else {
            return $online->get((string)$id);
        }

    }
    public function getRedisData($redisKey, $id)
    {
        $redisKey = $this->fixPreKey . $redisKey;
        $online = $this->setHashKey($redisKey);
        return $online->get((string)$id);
    }

}