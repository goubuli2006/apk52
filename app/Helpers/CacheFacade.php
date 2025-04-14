<?php

namespace App\Helpers;

use Exception;

class CacheFacade
{
    /**
     * @throws Exception
     */
    public static function get(string $name, array $params = [], array $other = [])
    {
        return CacheHelper::getInstance()->get($name, $params,$other);
    }

    public static function hGet(string $name, array $params = [],$field, array $other = [])
    {
        return CacheHelper::getInstance()->hGet($name, $params,$field,$other);
    }
}