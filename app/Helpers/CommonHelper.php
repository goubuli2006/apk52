<?php

namespace App\Helpers;

use App\Enum\DeviceTypeEnum;

class CommonHelper
{
    public function getDeviceType() {
        $agent = $_SERVER['HTTP_USER_AGENT'] ? strtolower($_SERVER['HTTP_USER_AGENT']): '';

        if (strpos($agent, 'iphone') !== false ||  strpos($agent, 'ipd') !== false) {
            return DeviceTypeEnum::IOS_DEVICE_TYPE;
        }
        if (strpos($agent, 'android') !== false) {
            return DeviceTypeEnum::ANDRIOD_DEVICE_TYPE;
        }
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return DeviceTypeEnum::ANDRIOD_DEVICE_TYPE;
        }
        if(isset($_SERVER['HTTP_VIA'])){
            if(stristr($_SERVER['HTTP_VIA'], 'wap')){
                return DeviceTypeEnum::ANDRIOD_DEVICE_TYPE;
            }
        }

        if (empty($agent)) {
                return DeviceTypeEnum::PC_DEVICE_TYPE;
            } elseif ( strpos($agent, 'mobile') !== false 
                || strpos($agent, 'android')    !== false
                || strpos($agent, 'silk/')      !== false
                || strpos($agent, 'kindle')     !== false
                || strpos($agent, 'blackberry') !== false
                || strpos($agent, 'opera mini') !== false
                || strpos($agent, 'opera mobi') !== false ) {
                return DeviceTypeEnum::ANDRIOD_DEVICE_TYPE;
        } else {
            return DeviceTypeEnum::PC_DEVICE_TYPE;
        }
    }
}