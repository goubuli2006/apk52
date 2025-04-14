<?php

namespace App\Services;
use App\Enum\DeviceTypeEnum;
use App\Helpers\CommonHelper;
use App\Services\AdService;
use App\Enum\PcEnum;
class CommonService extends BaseService
{
    protected static $view;
    public static function init()
    {
        self::$view = \Config\Services::renderer();
    }

    public function getHashCode($id, $type, $current)
    {
        $apiKey = "iu^Zvw0&tVySGaYJ";
        $token = hash_hmac('sha1', "id=$id&type=$type&time=$current", $apiKey, true);
        $find = array('+', '/');
        $replace = array('-', '_');
        $token = str_replace($find, $replace, base64_encode($token));

        return $token;
    }

    function getMd5Code($uri,$time){
        $apiKey="iu^Zvw0&t1VySGaYJ";
        //模式C   http://$domain/$uri/$key/$time
        $md5  = md5($uri.$apiKey.$time);
        return $md5;
    }

    /**
     * Undocumented function
     *
     * @param integer $id 父id
     * @param integer $cid 子id
     * @param array $data 可以直接传递seo_ data
     * @return void
     */
    public static function commonTdkToTemplate($id = 0, $cid = 0, $data = [])
    {
        $ployCateSevice = new PloyCategoryService();
        $view           = self::$view;
        $info           = [
            'title'       => '',
            'description' => '',
            'keywords'    => ''
        ];
        if (!empty($data)) {
            $info = [
                'title'       => isset($data['seo_title']) ? $data['seo_title'] : '',               //  default
                'description' => isset($data['seo_description']) ? $data['seo_description'] : '',
                'keywords'    => isset($data['seo_keywords']) ? $data['seo_keywords'] : '',
            ];
        } else {
            $data = $ployCateSevice->getCacheCategofyByIdOrCatalog($id, $cid);
            if (!empty($data)) {
                $info['title']       = $data['seo_title'];
                $info['description'] = $data['seo_description'];
                $info['keywords']    = $data['seo_keywords'];
            }
        }
        $view->setData(['tdk' => $info]);
    }

      /**
     * Undocumented function 模板公共参数
     *
     * @return void
     */
    public static function commonDataToTemplate()
    {
        $view = self::$view;
        $type = (new CommonHelper())->getDeviceType();
        if ($type == DeviceTypeEnum::PC_DEVICE_TYPE) {
            $info = self::pcCommonData();
        } else {
            $info =  self::mobileCommonData();
        }
        $view->setData($info);
        return $info;
    }
    protected function checkStrStartorEenWith(string $str, $delimiter = "/", $start = true): bool
    {
        if ($start && substr($str, 0) == $delimiter) {
            return true;
        }
        if (!$start && substr($str, -1) == $delimiter) {
            return true;
        }
        return false;
    }
    public static function pcCommonData()
    {
        $url = env("app.pc.domainUrl");

        $info = [
            'domain'       => $url,
            'domainName'   => env('app.domainName'),
            'webJsPath'    => env('app.statics.domain.enable') ? $url . env('app.pcJs') : env('app.pcJs'),
            'webCssPath'   => env('app.statics.domain.enable') ? $url . env('app.pcCss') : env('app.pcCss'),
            'webImagePath' => env('app.statics.domain.enable') ? $url . env('app.pcImg') : env('app.pcImg'),
            'mobileUrl'   => str_replace(env('app.pc.domainUrl'), env('app.mobile.domainUrl'), current_url()),
        ];
        return $info;
    }

    public static function mobileCommonData()
    {
        $url = env('app.mobile.domainUrl');
        $info = [
            'domain'       => $url,
            'domainName'   => env('app.domainName'),
            'webJsPath'    => env('app.statics.domain.enable') ? $url . env('app.mobileJs') : env('app.mobileJs'),
            'webCssPath'   => env('app.statics.domain.enable') ? $url . env('app.mobileCss') : env('app.mobileCss'),
            'webImagePath' => env('app.statics.domain.enable') ? $url . env('app.mobileImg') : env('app.mobileImg'),
            'pcUrl'        => str_replace(env('app.mobile.domainUrl'), env('app.pc.domainUrl'), current_url()),
        ];
        return $info;
    }

      /**
     * @param $navId int The id of the navigation
     * @param $childrenNavId int The id of the children Id 
     */
    public static function commonNavIdToTemplate ($navId, $childrenNavId = 0)
    {
        $view = self::$view;
        $info = [
            'navId'         => $navId,
            'childrenNavId' => $childrenNavId
        ];
        $view->setData($info);
    }

    public static function commonOtherDataToTemplate($data = [])
    {
        if (!empty($data)) {
            $view = self::$view;
            $view->setData($data);
        }
    }
}