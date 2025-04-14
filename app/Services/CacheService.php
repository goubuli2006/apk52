<?php

namespace App\Services;

use App\Helpers\CurlHelper;
use App\Services\RedisService;

class CacheService extends BaseService
{
    protected $guzzleClient = null;

    public function __construct()
    {
        $this->guzzleClient = (new CurlHelper());
    }

    //拉取列表keyApi
    const CKD_PULL_KEY_LIST_API = "/pullKeyList?number=%d";
    //拉取cache_log日志列表keyApi
    const CKD_PULL_LOG_KEY_LIST_API = "/pullLogList?number=%d";
    //拉取metric列表keyApi
    const CKD_PULL_METRIC_LIST_API = "/pullMetricList?number=%d";

    //拉取列表keyApi
    public function pullKeyList($number = 10)
    {
        $webDomain = $this->getRequestDomain();
        $url = sprintf($webDomain . self::CKD_PULL_KEY_LIST_API, $number);
        try {
            $res = $this->guzzleClient->httpReq($url);
            $response = isset($res['response']) ? $res['response'] : [];
            if (empty($response)){
                return json_encode(['code' => 500, 'msg' => '拉取失败']);
            }
            return $response;
        } catch (\Exception $e) {
            return json_encode(['code' => 500, 'msg' => $e->getMessage()]);
        }
    }
    //拉取cache_log日志列表keyApi
    public function pullLogList($number = 10)
    {
        $webDomain = $this->getRequestDomain();
        $url = sprintf($webDomain . self::CKD_PULL_LOG_KEY_LIST_API, $number);
        try {
            $res = $this->guzzleClient->httpReq($url);
            $response = isset($res['response']) ? $res['response'] : [];
            if (empty($response)){
                return json_encode(['code' => 500, 'msg' => '拉取失败']);
            }
            return $response;
        } catch (\Exception $e) {
            return json_encode(['code' => 500, 'msg' => $e->getMessage()]);
        }
    }
    //拉取metric列表keyApi
    public function pullMetricList($number = 10)
    {
        $webDomain = $this->getRequestDomain();
        $url = sprintf($webDomain . self::CKD_PULL_METRIC_LIST_API, $number);
        try {
            $res = $this->guzzleClient->httpReq($url);
            $response = isset($res['response']) ? $res['response'] : [];
            if (empty($response)){
                return json_encode(['code' => 500, 'msg' => '拉取失败']);
            }
            return $response;
        } catch (\Exception $e) {
            return json_encode(['code' => 500, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 请求info接口
     *
     * @param
     */
    public function getCachInfo()
    {
        try {
            $redis = RedisService::get();
            $info =  $redis->info();
            return json_encode(['data' => $info, 'msg' => 'success', 'code' => 200]);
        } catch (\Exception $e) {
            return json_encode(['data' => [], 'msg' => $e->getMessage(), 'code' => 500]);
        }
    }

    /**
     * 获取域名
     *
     * @param integer $webId
     * @return string
     */
    public function getRequestDomain()
    {
        $domain = "";
        $env = env('CI_ENVIRONMENT');
        if ($env == 'testing') {
            $domain = env('CK_DEV_REQ_DOMAIN');
        } else if ($env == 'production') {
            $domain = env('CK_PROD_REQ_DOMAIN');
        }
        return $domain;
    }
}