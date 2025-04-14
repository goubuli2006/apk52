<?php

namespace App\Controllers\Api;

use App\Helpers\CacheHelper;
use Websitelibrary\CacheApi\CacheApiService;
use CodeIgniter\HTTP\IncomingRequest;
use App\Services\CacheService;
use Websitelibrary\CacheApi\Utils;

class CacheApi extends BaseController
{
    /**
     * 模糊/精确 匹配获取key
     */
    public function pregRedisList()
    {
        CacheApiService::getInstance()->pregRedisList($this->request);
    }


    /**
     * 根据 key 获取 redis 数据
     */
    public function getDataByKey()
    {
        CacheApiService::getInstance()->getDataByKey($this->request);
    }

    /**
     * 根据 key 删除 redis 数据
     */
    public function delDataByKey()
    {
        CacheApiService::getInstance()->delDataByKey($this->request);
    }

    /**
     * 获取所有的key列表
     */
    public function getKeyList()
    {
        $data = (new CacheHelper())->register;

        $result = array_map(function ($k, $v) {
            return [
                'key' => $k,
                'doc' => $v['doc'],
                'ttl' => $v['ttl'] ?? '-1',
            ];
        }, array_keys($data), $data);

        return json_encode([
            'code' => 200,
            'msg' => 'success',
            'data' => $result
        ]);
    }

    public function health()
    {
        CacheApiService::getInstance()->ping();
    }

    /**
     * 同步缓存列表
     *
     */
    public function syncCacheList()
    {
        // get
        $input = $this->request->getGet();
        $callback = isset($input['callback']) ? $input['callback'] : "";
        $number = isset($input['number']) ? $input['number'] : 10;
        $time = isset($input['time']) ? $input['time'] : "";

        if (empty($callback)) {
            return json_encode(array('msg' => "参数不合法！", 'code' => 4001));
        }
        // header
        // Utils::getInstance()->checkHeaderParams($this->request, $time);

        $obj = new CacheService();
        if (method_exists($obj, $callback)) {
            return $obj->$callback($number);
        } else {
            return json_encode(array('msg' => "callback参数异常！", 'code' => 4002));
        }
    }

}