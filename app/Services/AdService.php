<?php namespace App\Services;

use App\Enum\PcEnum;
use App\Models\AdvertModel;
use App\Models\AdvertpicModel;
use App\Models\AppListModel;
use App\Models\GameListModel;
use Config\Custom;

/**
 * 广告位
 */
class AdService extends BaseService
{
    protected $advertPicModel;
    protected $advertModel;


    public function __construct()
    {
        $this->advertPicModel = new AdvertpicModel();
        $this->advertModel = new AdvertModel();
    }

    /**
     * @function getAdvert: 获取广告位数据
     */
    public function getAdvert($id, $field = 'url,`describe`,img,remark,type', $limit = 10, $offset = 0, $orderBy = 'no desc')
    {
        $data = $this->advertPicModel->getTableList("adid = {$id} and status = 1 and is_del = 0",  $field, $limit, $offset, $orderBy);
        return $this->getShowData($data);
    }


    public function getShowData(array $data): array
    {
        if ($data) {
            foreach ($data as $key => &$val) {
                if (isset($val['img']) && !empty($val['img'])) {
                    $val['img'] = env('app.volc.uploadDomain') . $val['img'];
                }
            }
        }
        return $data;
    }

    /**
     * @function advGameOrAppPack: 根据广告位id返回对应系统的游戏列表
     * @param $advId    广告位id
     * @param string $field 默认字段
     * @param int $classify 1游戏 2应用
     */
    public function advGameOrAppPack($advId, $classify, $isios, $field = 'remark')
    {
        $list = [];

        $adv = $this->advertPicModel->getOneInfoByWhere([
            'adid' => $advId,
            'is_del' => 0,
            'status' => 1
        ], $field);

        if ($adv) {
            $gField = 'bgl.id,bgl.name,bgl.title,bgl.addtime,bgl.uptime,bgl.type,IFNULL(icon,ifnull(icon,"")) as icon,bgl.description,bgl.tdown,bgl.comment,bgl.tags,bgl.classify,bgl.recom,';
            //根据系统查询下载包字段
            $gField .= $isios == 1 ? 'round(bgp.ios_size) as size,bgp.ios_url as downurl,ios_unit as unit' : 'round(bgp.and_size) as size,bgp.and_url as downurl,and_unit as unit';

            $gidStr = trim($adv['remark'], ',');

            if (!empty($gidStr)) {
                if ($classify == 1) {
                    $gameListModel = new GameListModel();

                    $list = $gameListModel->getNewGameList("bgl.id in ($gidStr) and bgl.state = 1 and bgl.status=1", [], $gField, 50, 0, "field(bgl.id,$gidStr)");

                } else {
                    $appListModel = new AppListModel();


                    $list = $appListModel->getNewAppList("bgl.id in ($gidStr) and bgl.state = 1 and bgl.status=1", [], $gField, 50, 0, "field(bgl.id,$gidStr)");

                }

            } else {
                $list = array();
            }
        }

        return $this->getFormatData($list);
    }


    public function advOtherGameOrAppPack($classify, $isios,$gameids)
    {
        $list = [];

        $gField = 'bgl.id,bgl.name,bgl.addtime,bgl.uptime,bgl.type,IFNULL(icon,ifnull(icon,"")) as icon,bgl.description,bgl.tdown,bgl.comment,bgl.tags,bgl.classify,bgl.union_id,bgl.recom,';
        //根据系统查询下载包字段
        $gField .= $isios == 1 ? 'round(bgp.ios_size) as size,bgp.ios_url as downurl,ios_unit as unit' : 'round(bgp.and_size) as size,bgp.and_url as downurl,and_unit as unit';

        $gidStr = trim($gameids, ',');

        if (!empty($gidStr)) {
            if ($classify == 1) {
                $gameListModel = new GameListModel();

                $list = $gameListModel->getNewGameList("bgl.id in ($gidStr) and bgl.state = 1 and bgl.status=1 ", [], $gField, 50, 0, "field(bgl.id,$gidStr)");

            } else {
                $appListModel = new AppListModel();


                $list = $appListModel->getNewAppList("bgl.id in ($gidStr) and bgl.state = 1 and bgl.status=1 ", [], $gField, 50, 0, "field(bgl.id,$gidStr)");

            }

        } else {
            $list = array();
        }

        return $this->getFormatData($list);
    }

    /**
     * 根据广告位id获取相应游戏或者应用list
     */
    public function getAdvGameOrAppPackList($advId, $isios = 0, $field = "remark,type")
    {
        $list = [];

        $adv = $this->advertPicModel->getOneInfoByWhere([
            'adid' => $advId,
            'is_del' => 0,
            'status' => 1
        ], $field);

        $classify = $adv['type'];
        $gidStr = trim($adv['remark'], ',');
        if (!empty($gidStr)) {
            $list =  $this->advOtherGameOrAppPack($classify, $isios, $gidStr);
        }
        return $list;
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

                $typeInfo = $this->getTypeInfoUrl($val['type'], $val['classify']);
                $val['type_info'] = $val['type_info'] = '';
                if (!empty($typeInfo)) {
                    $val['type_info'] = $typeInfo['name'];
                    $val['type_href'] = $typeInfo['href'];
                }

                if (isset($val['uptime']) && !empty($val['uptime'])) {
                    $val['uptime_format'] = date('Y-m-d H:i:s', $val['uptime']);
                }

                $val['star'] = mt_rand(7,10);

                if (isset($val['size']) && !empty($val['size'])) {
                    $packUnit = Custom::getPackUnit();
                    if(isset($val['unit']) && !empty($val['unit'])){
                        $val['size_format'] = $val['size'] . $packUnit[$val['unit']];

                    }else{
                        $val['size_format'] = $val['size'] . $packUnit['1'];
                    }
                } else {
                    $val['size'] = $val['size_format'] = 0;
                }
                $val['rand_dom'] = mt_rand(1000,5000);
                $result[$key] = $val;
            }
        }

        return $result;
    }

    public function getAdvertIdByGameList($advertId){
        $topGameList = $this->getAdvert($advertId);
        foreach ($topGameList as $k => $val) {
            $gidArr = explode(',', $val['remark']);
            $gidStr = isset($gidArr[0])?$gidArr[0]: '';
            $ginfo = $this->advOtherGameOrAppPack($val['type'], 0, $gidStr);
            $topGameList[$k]['gameInfo'] = isset($ginfo[0]) ? $ginfo[0] : [];
        }

        return $topGameList;
    }

}