<?php

namespace App\Helpers;

class CurlHelper
{
    
    protected $config = [
        'connect_timeout' => 3, //连接超时时间
        'timeout' => 5, //请求超时时间
    ];

    public function __construct($config = [])
    {
        $this->config = array_merge($this->config,$config);
    }
    /**
     * curl请求
     *
     * @param [type] $url
     * @param [type] $data
     * @param [type] $info
     * @return [type]
     */
    public function httpReq($url, $data = null, $info = '')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSLVERSION, false);

        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->config['connect_timeout']);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->config['timeout']);
        if ($info == 1) {
            curl_setopt($curl, CURLOPT_HEADER, 1);//获取http头信息
            curl_setopt($curl, CURLOPT_NOBODY, 1);//不返回html的body信息
        }
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl); //响应体
        $res = array();
        $res['head'] = curl_getinfo($curl);
        // 获取状态码
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $res['status_code'] = $status_code;
        $res['curl_errno'] = curl_errno($curl);
        $res['curl_error'] = curl_error($curl);
        $res['response'] = $output;
        return $res;
    }
}
