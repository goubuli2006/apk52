<?php

namespace App\Controllers\Mobile;

use App\Services\RedisService;
use CodeIgniter\Controller;

class CacheRedisFile extends Controller
{
    protected \Redis $redis;

    protected $password = "lEBbPSMA8#budvGO@@3fAbwJy6PzBy8c";

    public function __construct()
    {
        $this->redis = RedisService::get();
    }

    public function pregRedisList()
    {
        header('Access-Control-Allow-Origin: *');

        $input = $this->request->getPost();
        $searchKey = $input['searchKey'] ?? "";
        $token = $input['token'] ?? "";
        $time = $input['time'] ?? "";
        if (empty($searchKey) || empty($token) || empty($time)) {
            $this->returnSuccess(400, '参数不能为空', array());
        }

        if (time()-$time>120){
            $this->returnSuccess(400, 'token已过期', array());
        }

        $sign = md5($searchKey.$this->password. $time);

        if ($sign!=$token){
            $this->returnSuccess(400, 'token已过期', array());
        }

        // 1精确匹配  2模糊匹配
        $searchMethod = $input['searchMethod'] ? $input['searchMethod'] : 1;

        $searchKey = htmlspecialchars(strip_tags($searchKey));

        if ($searchMethod == 1) {

        } else if ($searchMethod == 2) {
            $searchKey = "*" . $searchKey . "*";
        }

        $list = $this->scanAllForMatch($searchKey);

        if (empty($list)) {
            $this->returnSuccess(300, '暂无数据', array());
        } else {
            $this->returnSuccess(200, 'success', $list);
        }
    }

    public function getRedisData()
    {
        header('Access-Control-Allow-Origin: *');

        $input = $this->request->getPost();
        $searchKey = $input['searchKey'] ?? "";
        $token = $input['token'] ?? "";
        $time = $input['time'] ?? "";
        if (empty($searchKey) || empty($token) || empty($time)) {
            $this->returnSuccess(400, '参数不能为空', array());
        }

        if (time()-$time>120){
            $this->returnSuccess(400, 'token已过期', array());
        }

        $sign = md5($searchKey.$this->password. $time);

        if ($sign!=$token){
            $this->returnSuccess(400, 'token已过期', array());
        }

        $info = $this->redRedisData($this->redis->type($searchKey),$searchKey);

        if ($this->isJson($info)){
            $info = json_decode($info,true);
        }

        $html = print_r($info,true);
        $this->returnSuccess(200, '', $html);

    }

    public function pregRedisDelKey()
    {
        header('Access-Control-Allow-Origin: *');

        $input = $this->request->getPost();
        $searchKey = $input['searchKey'] ?? "";
        $token = $input['token'] ?? "";
        $time = $input['time'] ?? "";
        if (empty($searchKey) || empty($token) || empty($time)) {
            $this->returnSuccess(400, '参数不能为空', array());
        }

        if (time()-$time>120){
            $this->returnSuccess(400, 'token已过期', array());
        }

        $sign = md5($searchKey.$this->password. $time);

        if ($sign!=$token){
            $this->returnSuccess(400, 'token已过期', array());
        }

        $flag = $this->redis->del($searchKey);
        if ($flag){
            $this->returnSuccess(200, '删除成功', array());
        }else{
            $this->returnSuccess(400, '删除失败', array());
        }
    }

    public function returnSuccess($code, $msg, $data)
    {
        $returnData['code'] = $code;
        $returnData['message'] = $msg;
        $returnData['data'] = $data;

        echo json_encode($returnData);
        die;
    }

    function isJson($string)
    {
        return ((is_string($string) &&
            (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }

    function scanAllForMatch($pattern, $cursor = null, $count = 100)
    {
        $this->redis->setOption(\Redis::OPT_SCAN, \Redis::SCAN_RETRY);
        $batch = 1000;

        $result = array();
        while ($keys = $this->redis->scan($cursor,  $pattern,  $batch)) {
            $result = array_merge($result, $keys);

            if (count($result) >= $count) {
                break;
            }
        }

        return $result;
    }

    public function redRedisData($type,$key){

        if ($type==\Redis::REDIS_STRING){
            return $this->redis->get($key);
        }else if ($type==\Redis::REDIS_SET){
            return $this->redis->sMembers($key);
        }else if ($type==\Redis::REDIS_LIST){
            return $this->redis->lRange($key, 0, -1);
        }else if ($type==\Redis::REDIS_ZSET){
            return $this->redis->zRange($key, 0, -1);
        }else if ($type==\Redis::REDIS_HASH){
            return $this->redis->hgetall($key);
        }else{
            return  'TYPE REDIS_NOT_FOUND';
        }
    }

}