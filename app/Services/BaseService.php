<?php namespace App\Services;

use App\Enum\CategoryEnum;
use CodeIgniter\Config\Services;
use Config\Custom;
class BaseService
{
    protected $service = null;
    protected $cache = null;

    public function __construct()
    {
        $this->cache = \Config\Services::cache();
    }

    /**
     * @function checkData: 检查参数是否正确
     */
    public function checkData($input = [])
    {
        return true;
    }

    public function getLocationUrl($id, $classify, $union_id = "")
    {
        $end = "";
        if ($classify == 1) {
            $end = env('app.game.end');
            $typeData['catalog'] = "game";
        } else {
            $end = env('app.app.end');
            $typeData['catalog'] = "app";
        }

        $location['href'] = env('app.domainUrl') . $typeData['catalog'] . "/"  . $union_id . $end;
        $location['more'] = env('app.domainUrl') . $typeData['catalog'] . "/";

        return $location;

    }

    /**
     * 获取主库href
     *
     * @param [int] $id 主库id
     * @param [type] $classify
     * @return string
     */
    public function getMasterLocation(int $id, $classify)
    {
        $master_href = '';
        if ($classify == 1) {
            $master_href = env('app.domainUrl') . 'zt/'  . $id . '_game/';
        } else {
            $master_href = env('app.domainUrl') . 'zt/'  . $id . '_soft/';
        }
        return $master_href;
    }

    /**
     * 
     * return int 
     */
    public function getPidByClassify($classify)
    {
        if ($classify == 1) {
            $pid = CategoryEnum::GAME_CATE_PID;
        } else {
            $pid = CategoryEnum::SOFT_CATE_PID;
        }
        return $pid;
    }

    public function getTypeInfoUrl($type, $classify)
    {

        $typeInfo = [];
        $cateService = new CategoryService();
        $typeData = $cateService->getInfoByCatalog($this->getPidByClassify($classify));
        if ($typeData && !empty($typeData['children'])) {
            foreach ($typeData['children'] as $key => $val) {
                if ($val['id'] == $type) {
                    $typeInfo['name'] = $val['name'];
                    $typeInfo['href'] = env('app.domainUrl') . $typeData['catalog'] . "/" . $val['catalog'] . '/';
                    break;
                }
            }
        }

        return $typeInfo;
    }

    public function getFormatData($list)
    {
        $result = [];
        if ($list) {
            foreach ($list as $key => $val) {

                if (isset($val['icon']) && !empty($val['icon'])) {
                    if ( strrpos($val['icon'],'http')===false && strrpos($val['icon'],'https')===false){
                        $val['icon'] = env('app.volc.uploadDomain') . $val['icon'];
                    }
                }

                $val['a_href'] = $this->getLocationUrl($val['id'], $val['classify'], $val['union_id'])['href'];
                $val['more'] = $this->getLocationUrl($val['id'], $val['classify'])['more'];
                $val['master_href'] = isset($val['mid']) ? $this->getMasterLocation($val['mid'], $val['classify']) : '';
                $typeInfo = $this->getTypeInfoUrl($val['type'], $val['classify']);

                $val['type_info'] = $val['type_href'] = $val['type_shortname'] = '';
                if (!empty($typeInfo)) {
                    $val['type_info'] = $typeInfo['name'];
                    $val['type_href'] = $typeInfo['href'];
                    $val['type_shortname'] = mb_substr($typeInfo['name'], 0, 2);
                }

                if (isset($val['uptime']) && !empty($val['uptime'])) {
                    $val['uptime_format'] = date('Y-m-d H:i:s', $val['uptime']);
                }

                $val['star'] = mt_rand(70,100)/10;

                if (isset($val['size']) && !empty($val['size'])) {
                    $packUnit = Custom::getPackUnit();
                    if(isset($val['unit']) && !empty($val['unit'])){
                        $val['size_format'] = $val['size'] . $packUnit[$val['unit']];

                    }else{
                        $val['size_format'] = $val['size'] . $packUnit['1'];
                    }
                } else {
                    $val['size'] = 0;
                    $val['size_format'] = 0;
                }
                $val['rand_dom'] = mt_rand(1000,5000);
                $val['mdown'] = mt_rand(5000,20000);
                $result[$key] = $val;
            }
        }
        return $result;
    }

    /**
     * 获取分类项href
     */
    public function getTypeInfoHref($classify)
    {
        $typeData = Custom::getCategory($classify);

        if ($typeData && !empty($typeData['children'])) {
            foreach ($typeData['children'] as $key => &$val) {
                $val['href'] = env('app.domainUrl') . $typeData['catalog'] . "/" . $val['catalog'] . '/';
                $val['more'] = env('app.domainUrl') . $typeData['catalog'] . "/";
                $val['cate_icon_name'] = "qb"; //传到前台的图标
                if ($val['catalog']) {
                    $val['cate_icon_name'] = mb_substr($val['catalog'], 0, 2);
                }
            }
        }
        return $typeData;
    }

    public function getImageFormatData($list)
    {
        $result = [];
        if ($list) {
            foreach ($list as $key => $val) {
                if ($val['path']) {
                    $val['path'] = env('app.volc.uploadDomain') . $val['path'];
                    $result[$key] = $val;
                }
            }
        }

        return $result;
    }
}