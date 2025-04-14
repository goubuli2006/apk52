<?php namespace App\Services;

use App\Enum\PolyCatEnum;
use App\Helpers\CacheFacade;
use App\Helpers\CacheHelper;
use App\Helpers\CommonHelper;
use App\Models\AppListModel;
use App\Models\GameListModel;
use App\Models\TagModel;
use Config\Custom;

/**
 * 专题
 */
class TagService extends BaseService
{
    public $tagModel;

    public function __construct()
    {
        $this->tagModel = new TagModel();
    }

    public function getTagRow($where, $field)
    {
        return $this->tagModel->getOneInfoByWhere($where, $field);
    }

    public function getTagList($type, $field, $limit, $order = 'uptime desc')
    {
        $data = $this->tagModel->getTableList("status=1 and type='{$type}'", $field, $limit, 0, $order);
        return $this->getTagFormatData($data);
    }

    public function getNewsList($field, $limit, $where = "status=1", $offset = 0, $flag = false, $gamelimit = 6,$order='uptime desc' )
    {
        $data = $this->tagModel->getTableList($where, $field, $limit, $offset, $order);
        return $this->getTagFormatData($data, $flag, $gamelimit);
    }

    public function getTagListByIdIn($where, $field, $limit)
    {
        $data = $this->tagModel->getTableList($where, $field, $limit, 0, "uptime desc");
        return $this->getTagFormatData($data);
    }


    public function getTagFormatData(array $data, $flag = false, $gameLimit = 6)
    {
        $result = [];

        $expGid = 0;
        if ($data) {
            foreach ($data as $key => $val) {
                if (isset($val['uptime']) && !empty($val['uptime'])) {
                    $val['uptime_format'] = date('Y-m-d H:i:s', $val['uptime']);
                }

                // 教程
                $location = $this->getTagLocation($val['catalog'], PolyCatEnum::APP_POLY_CAT_TAGS);

                if ($location) {
                    $val['href'] = $location['href'];
                    $val['more'] = $location['more'];
                }
                if (isset($val['img']) && !empty($val['img'])) {
                    if (strrpos($val['img'], 'http') === false && strrpos($val['img'], 'https') === false) {
                        $val['img'] = env('app.volc.uploadDomain') . $val['img'];
                    }
                }

                if ($flag) {
                    $val['total'] = 0;
                    $val['gameList'] = array();
                    $where = "classify = {$val['type']} and status = 1 and state=1 and tagid={$val['id']}";
                    $gameList = "";
                    if ($val['type'] == 1) {
                        $gameList = new GameListModel();
                        if ($val['game_num'] > 0) {
                            $totlerows_tag = $val['game_num'];
                        } else {
                            $totlerows_tag = $gameList->getCountByTag($where);   //分页的总记录数
                        }
                    } else {
                        $gameList = new AppListModel();

                        if ($val['game_num'] > 0) {
                            $totlerows_tag = $val['game_num'];
                        } else {
                            $totlerows_tag = $gameList->getCountByTag($where);   //分页的总记录数
                        }
                    }

                    $gcount = $totlerows_tag ? $totlerows_tag : 0;
                    $val['gamecount'] = $val['total'] = $gcount;
                    if ($val['type'] == 1) {
                        $val['gameList'] = $gameList->getListByTag($where, 'bgl.id,name,icon,type,classify,bgl.uptime,union_id', $gameLimit, 0, 'uptime desc');
                    } else {
                        $val['gameList'] = $gameList->getListByTag($where, 'bgl.id,name,icon,type,classify,bgl.uptime,union_id', $gameLimit, 0, 'uptime desc');
                    }
                    if ($val['gameList']) {
                        $val['gameList'] = $this->getFormatData($val['gameList']);
                    }

                }

                $result[$key] = $val;
            }
        }


        return $result;
    }

    public function getTagGameDetailCache(...$params)
    {
        $gameService = new GameService();
        $gameList = new GameListModel();

        list($tagInfo, $expGid) = $params;

        if (!empty($expGid)) {
            $where = " and bgl.id != {$expGid} and tagid={$tagInfo['id']}";
            $countWhere = " status=1 and state=1  and id != {$expGid} and tagid={$tagInfo['id']}";
        } else {
            $where = " and tagid={$tagInfo['id']}";
            $countWhere = " status=1 and state=1  and tagid={$tagInfo['id']}";
        }

        $isios = (new CommonHelper())->getDeviceType();
        $gfields = 'bgl.id,bgl.name,bgl.title,bgl.addtime,bgl.uptime,bgl.type,IFNULL(icon,ifnull(icon,"")) as icon,bgl.description,bgl.tdown,bgl.comment,bgl.tags,bgl.classify,union_id,';
        //根据系统查询下载包字段
        $gfields .= $isios == 1 ? 'round(bgp.ios_size) as size,bgp.ios_url as downurl,ios_unit as unit' : 'round(bgp.and_size) as size,bgp.and_url as downurl,and_unit as unit';

        $glist = $gameService->getListByTag($gfields, 5, 'bgl.uptime desc', $where);
        $val['gameList'] = $glist;
        if ($tagInfo['game_num'] > 0) {
            $val['game_num'] = $tagInfo['game_num'];
        } else {
            $val['game_num'] = $gameList->getCountByTag($countWhere);
        }
        $val['gamecount'] = $val['game_num'];

        return $val;
    }

    public function getTagAppDetailCache(...$params)
    {
        $appService = new AppService();
        $appList = new AppListModel();

        list($tagInfo, $expGid) = $params;

        if (!empty($expGid)) {
            $where = " and bgl.id != {$expGid} and tagid={$tagInfo['id']}";
            $countWhere = " status=1 and state=1  and id != {$expGid} and tagid={$tagInfo['id']}";
        } else {
            $where = " and tagid={$tagInfo['id']}";
            $countWhere = " status=1 and state=1  and tagid={$tagInfo['id']}";
        }

        $isios = (new CommonHelper())->getDeviceType();
        $gfields = 'bgl.id,bgl.name,bgl.title,bgl.addtime,bgl.uptime,bgl.type,IFNULL(icon,ifnull(icon,"")) as icon,bgl.description,bgl.tdown,bgl.comment,bgl.tags,bgl.classify,union_id,';
        //根据系统查询下载包字段
        $gfields .= $isios == 1 ? 'round(bgp.ios_size) as size,bgp.ios_url as downurl,ios_unit as unit' : 'round(bgp.and_size) as size,bgp.and_url as downurl,and_unit as unit';

        $glist = $appService->getListByTag($gfields, 5, 'bgl.uptime desc', $where);
        $val['gameList'] = $glist;
        if ($tagInfo['game_num'] > 0) {
            $val['game_num'] = $tagInfo['game_num'];
        } else {
            $val['game_num'] = $appList->getCountByTag($countWhere);
        }
        $val['gamecount'] = $val['game_num'];

        return $val;
    }

    public function getTagLocation($catalog, $polyId)
    {
        $location = [];
        $polyInfo = Custom::getPloyCatData($polyId);

        $location['more'] = env('app.domainUrl') . $polyInfo['catalog'] . "/";
        $location['href'] = env('app.domainUrl') . $polyInfo['catalog'] . "/" . $catalog . env('app.zt.end');

        return $location;
    }

    public function getTagsTdk($polyId)
    {
        $polyInfo = $this->getTagsCategoryInfo($polyId);

        $tdk = [];

        if (isset($polyInfo['seo_title']) && !empty($polyInfo['seo_title'])) {
            $tdk['title'] = $polyInfo['seo_title'];
        }

        if (isset($polyInfo['seo_keywords']) && !empty($polyInfo['seo_keywords'])) {
            $tdk['keywords'] = $polyInfo['seo_keywords'];
        }

        if (isset($polyInfo['seo_description']) && !empty($polyInfo['seo_description'])) {
            $tdk['description'] = $polyInfo['seo_description'];
        }

        return $tdk;
    }

    public function getTagsCategoryInfo($polyId)
    {
        return Custom::getPloyCatData($polyId);
    }

    public function getTagsListCount($where)
    {
        return $this->tagModel->getTableListCount($where);
    }

    /**
     * @function getPageUrl:
     * @param $type 0全部 1游戏  2应用
     */
    public function getPageUrl($type)
    {
        $polyInfo = $this->getTagsCategoryInfo(PolyCatEnum::APP_POLY_CAT_TAGS);

        if ($type == 0) {
            return '/' . $polyInfo['catalog'] . '/';
        }

        $childInfo = [];

        if ($type == 1) {
            $childInfo = $this->getTagsCategoryInfo(PolyCatEnum::APP_POLY_CAT_TAGS_GAME);
        }

        if ($type == 2) {
            $childInfo = $this->getTagsCategoryInfo(PolyCatEnum::APP_POLY_CAT_TAGS_APP);
        }

        return '/' . $polyInfo['catalog'] . '/' . $childInfo['catalog'] . '/';

    }

    public function getAllNewsLocation()
    {
        $data = [];

        for ($i = 0; $i < 3; $i++) {
            $data[$i] = $this->getPageUrl($i);
        }
        return $data;
    }

    /**
     * 获取游戏或者应用列表以及数量
     *
     * @param [type] ...$parmas
     * @return array
     */
    public function getTagGameOrAppListInfo(...$parmas) {
        $gfields = 'bgl.id,bgl.title,bgl.shortname,bgl.icon,bgl.type,bgl.classify,bgl.uptime,bgl.tags,bgl.name,bgl.comment,bgl.union_id,description,bgl.isshow';
        $deviceType = (new CommonHelper())->getDeviceType();
        $gfields .= $deviceType == 1 ? ',bgp.ios_size as size,bgp.ios_unit as unit,bgp.ios_url as downurl,bgp.ios_ver as ver' : ',bgp.and_size as size,bgp.and_unit as unit,bgp.and_url,bgp.and_url as downurl,bgp.and_ver as version';

        list($tagInfo, $limit, $offset, $service) = $parmas;
        $gameList = $service->getListByTag($gfields, $limit, 'uptime desc', " and tagid={$tagInfo['id']}", $offset);
        $gamecount = $tagInfo['game_num'];
        $gameListModel->getGameCountByTag($tableWhere);
        return [
            $gameList,
            $gamecount
        ];
    }
}