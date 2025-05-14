<?php

namespace App\Controllers\Mobile;

use App\Enum\CategoryEnum;
use App\Enum\MEnum;
use App\Helpers\CacheHelper;
use App\Models\AppListModel;
use App\Models\GameListModel;
use App\Services\AdService;
use App\Services\AppService;
use App\Services\CategoryService;
use App\Services\GameService;
use App\Services\RedisService;
use App\Services\TagService;

class Game extends BaseController
{

    protected \Redis $redis;

    public function __construct()
    {
        $this->redis = RedisService::get();
    }

    public function index()
    {
        $cateService = new CategoryService();
        $gameService = new GameService();
        $gameList    = new GameListModel();
        $pager       = \Config\Services::pager();
        $tagService  = new TagService();

        $tdk = $cateService->getTdkToTemplate(CategoryEnum::GAME_CATE_PID);
        $gameType     = $cateService->getInfoByCatalog(CategoryEnum::GAME_CATE_PID);
        $categoryList = isset($gameType['children'])?$gameType['children']:[];

        $params = [
            'page' => 1,
            'current_type' => '',
            'orderNum' => 1
        ];

        $gameParam = $this->request->uri->getSegment(2); //url -> category
        if ($gameParam) {
            if (is_numeric($gameParam)) {
                $params['page'] = $gameParam;
            } else {
                $pageS = $this->request->uri->getSegment(3); //1-1
                if ($pageS && !is_numeric($pageS)) {
                    list($params['orderNum'], $params['page']) = explode('-', $pageS);
                } else {
                    $params['page'] = $pageS ? : $params['page'];
                }
                $gameParam = explode('_',$gameParam)[1];
                $categoryInfo = $cateService->getInfoByCatalog(CategoryEnum::GAME_CATE_PID, $gameParam);
                if (empty($categoryInfo)) {
                    $this->show_404();
                }
                $params['current_type'] = $categoryInfo['catalog'];
                $tdk = $cateService->getTdkToTemplate(CategoryEnum::GAME_CATE_PID, $gameParam);
            }
        }
        $limit  = 20;
        $offset = ($params['page'] - 1) * $limit;


        if (empty($params['current_type'])) {
            $baseurl = '/' . $gameType['catalog'] . '/';
        } else {
            $baseurl = '/' . $gameType['catalog'] . '/game_' . $gameParam . '/';
        }

        $pager->setPath($baseurl);

        if (!empty($params['current_type'])) {
            $current_type_id = $cateService->getInfoByCatalog(CategoryEnum::GAME_CATE_PID, $params['current_type'])['id'];
            $where = " and classify = 1 and type='{$current_type_id}'";
            $countwhere = " status = 1 and state = 1 and classify = 1 and type='{$current_type_id}'";
        } else {
            $where = ' and classify = 1';
            $countwhere = ' status = 1 and state = 1 and classify = 1';
        }
        $gfields = "bgl.id,bgl.name,bgl.icon,bgl.type,bgl.classify,bgl.shortname,bgl.game_score,bgl.uptime,bgl.gameid,bgp.and_url as downurl,bgl.tags,bgl.union_id,bgp.and_size as size,comment,description";
        //列表
        $result    = $gameService->getGameList($gfields, $limit, 'uptime desc', $where, $offset);
        $totalRows = $gameList->getTableListCount($countwhere);                                    //分页的总记录数
        $links     = $pager->makeLinks($params['page'], $limit, $totalRows, 'default_m_all');
        $links     = str_replace(env('app.pc.domainUrl'), env('app.mobile.domainUrl'), $links);

        $tfield = 'id,name,full_name,catalog,type,img,gameid,uptime';
        $gameZtList = $tagService->getNewsList($tfield, 9,'type=1 and status=1 and state=2');

        $downGameList = $gameService->getGameList($gfields,9,'wview desc,uptime desc');
        $downGameList = array_chunk($downGameList, 3);

        $info = [
            'tdk'=>$tdk,
            'nav'=>2,
            'categoryList'=>$categoryList,
            'current_type'=>$params['current_type'],
            'list'=>$result,
            'links'=>$links,
            'tdk_detail'=>1,
            'gameZtList'=>$gameZtList,
            'downGameList'=>$downGameList,
        ];

        return view("/Statics/M/game/game.html", $info);
    }

    public function soft()
    {
        $cateService = new CategoryService();
        $gameService = new AppService();
        $gameList    = new AppListModel();
        $pager       = \Config\Services::pager();
        $tagService  = new TagService();

        $tdk = $cateService->getTdkToTemplate(CategoryEnum::SOFT_CATE_PID);

        $gameType     = $cateService->getInfoByCatalog(CategoryEnum::SOFT_CATE_PID);
        $categoryList = isset($gameType['children'])?$gameType['children']:[];

        $params = [
            'page' => 1,
            'current_type' => '',
            'orderNum' => 1
        ];

        $gameParam = $this->request->uri->getSegment(2); //url -> category
        //special case, app_vault is an app, not category
        $gameInfo  = $gameList->getGameInfoByUnionId($gameParam, true, true, true);

        if (!empty($gameInfo)) {

            $info = [];
            $this->dealSoftDetail($gameParam, $info);
            return view("/Statics/M/game/detail.html", $info);
        }
        
        if ($gameParam) {
            if (is_numeric($gameParam)) {
                $params['page'] = $gameParam;
            } else {
                $pageS = $this->request->uri->getSegment(3); //1-1
                if ($pageS && !is_numeric($pageS)) {
                    list($params['orderNum'], $params['page']) = explode('-', $pageS);
                } else {
                    $params['page'] = $pageS ? : $params['page'];
                }
                $gameParam = explode('_',$gameParam)[1];
                $categoryInfo = $cateService->getInfoByCatalog(CategoryEnum::SOFT_CATE_PID, $gameParam);
                if (empty($categoryInfo)) {
                    $this->show_404();
                }
                $params['current_type'] = $categoryInfo['catalog'];
                $tdk = $cateService->getTdkToTemplate(CategoryEnum::SOFT_CATE_PID, $gameParam);
            }
        }
        $limit  = 20;
        $offset = ($params['page'] - 1) * $limit;

        if (empty($params['current_type'])) {
            $baseurl = '/' . $gameType['catalog'] . '/';
        } else {
            $baseurl = '/' . $gameType['catalog'] . '/app_' . $gameParam . '/';
        }
        $pager->setPath($baseurl);

        if (!empty($params['current_type'])) {
            $current_type_id = $cateService->getInfoByCatalog(CategoryEnum::SOFT_CATE_PID, $params['current_type'])['id'];
            $where = " and classify = 2 and type='{$current_type_id}'";
            $countwhere = " status = 1 and state = 1 and classify = 2 and type='{$current_type_id}'";
        } else {
            $where = ' and classify = 2';
            $countwhere = ' status = 1 and state = 1 and classify = 2';
        }
        $gfields = "bgl.id,bgl.name,bgl.icon,bgl.type,bgl.classify,bgl.shortname,bgl.game_score,bgl.uptime,bgl.gameid,bgp.and_url as downurl,bgl.tags,bgl.union_id,bgp.and_size as size,comment,description";
        //列表
        $result    = $gameService->getAppList($gfields, $limit, 'uptime desc', $where, $offset);
        $totalRows = $gameList->getTableListCount($countwhere);                                    //分页的总记录数
        $links     = $pager->makeLinks($params['page'], $limit, $totalRows, 'default_m_all');
        $links     = str_replace(env('app.pc.domainUrl'), env('app.mobile.domainUrl'), $links);

        $tfield = 'id,name,full_name,catalog,type,img,gameid,uptime';
        $gameZtList = $tagService->getNewsList($tfield, 9,'type=2 and status=1 and state=2');

        $downGameList = $gameService->getAppList($gfields,9,'wview desc,uptime desc');
        $downGameList = array_chunk($downGameList, 3);

        $info = [
            'tdk'=>$tdk,
            'nav'=>3,
            'categoryList'=>$categoryList,
            'current_type'=>$params['current_type'],
            'list'=>$result,
            'links'=>$links,
            'tdk_detail'=>1,
            'gameZtList'=>$gameZtList,
            'downGameList'=>$downGameList,
        ];
        return view("/Statics/M/game/app.html", $info);
    }

    public function gameDetail($union_id = '')
    {
        $info = [];
        $this->dealGameDetail($union_id, $info);
        return view("/Statics/M/game/detail.html", $info);
    }

    public function softDetail($union_id = '')
    {
        $info = [];
        $this->dealSoftDetail($union_id, $info);
        return view("/Statics/M/game/detail.html", $info);
    }

    protected function dealGameDetail($union_id, &$info){

        $types = $this->request->uri->getSegment(1);

        $gameListModel = new GameListModel();
        $gameService   = new GameService();
        $tagService    = new TagService();
        $adService     = new AdService();

        $gameInfo      = $gameListModel->getGameInfoByUnionId($union_id, true, true, true);

        if (empty($gameInfo)) {
            $this->show_404();
        }
        $tdk = [
            'title'=>$gameInfo['title'],
            'keywords'=>$gameInfo['keywords'],
            'description'=>$gameInfo['description']
        ];
        $gameInfo['star'] = number_format(mt_rand(70,99) /10,1);
        $gameInfo['downDetail'] = '/game/'.$union_id.'/download';

        //更新浏览次数
        $time = date('YmdH0000');

        $this->redis->HINCRBY(env('SITE_ID', 0).":".CacheHelper::GAME_VIEW_DETAIL.":".$time.":mview",$gameInfo['id'],1);
        $this->redis->HINCRBY(env('SITE_ID', 0).":".CacheHelper::GAME_VIEW_DETAIL.":".$time.":wview",$gameInfo['id'],1);
        $this->redis->HINCRBY(env('SITE_ID', 0).":".CacheHelper::GAME_VIEW_DETAIL.":".$time.":twview",$gameInfo['id'],1);

        $gfield = 'bgl.id,bgl.title,bgl.shortname,bgp.and_url,bgl.icon,bgl.type,bgl.classify,bgl.uptime,bgp.and_size as size,bgp.and_unit as unit,bgl.tags,bgl.name,bgl.comment,bgl.union_id';
        $tfield = 'id,name,full_name,catalog,type,img,gameid,uptime';

        //相关专题 取id最小
        $tagsLikeData = [];
        if(!empty($gameInfo['tags'])){
            $likeTagWhere = 'status = 1 and id in ('.$gameInfo['tags'] . ')';
            $tagFields    = 'id,name,title,catalog,img,full_name,img,type';
            $tagsLikeData = $tagService->getNewsList($tagFields, 1, $likeTagWhere,0,false,0,'id asc');
        }

        $tagsLikeData = $tagsLikeData ? $tagsLikeData[0] : [];
        if(!empty($tagsLikeData)){
            $where = "status = 1 and state=1 and tagid={$tagsLikeData['id']}";
            $tagsLikeData['gamecount'] = $gameListModel->getCountByTag($where);
        }

        //开发者其他游戏
        $platGameList = $gameService->getGameList($gfield,6,'bgl.uptime desc'," and platid = {$gameInfo['platid']} and bgl.id != {$gameInfo['id']}");
        //热门标签
        $twhere = 'status = 1 and state=2 and type = 1' ;
        $hotZtList = $tagService->getNewsList($tfield,9,$twhere);

        //相关版本 主库相关
        if ($gameInfo['gameid']) {
            $aboutWhere = " and bgl.gameid = " .$gameInfo['gameid'];
            $aboutField = $gfield . ',bgp.and_ver';
            $gameAboutVersion = $gameService->getGameList($aboutField, 10, 'bgl.uptime desc', $aboutWhere);
        }

        //类似游戏
        if(!empty($tagsLikeData)){
            $aboutGameList = $gameService->getListByTag($gfield,6,'uptime desc',"  and bgl.id != {$gameInfo['id']} and t.tagid = {$tagsLikeData['id']}");
        }

        //好游安利
        $goodGameList = $adService->getAdvGameOrAppPackList(MEnum::M_GAME_DETAIL_GOOD);

        $info = [
            'nav'=> 2.1,
            'tdk'=>$tdk,
            'gInfo'=>$gameInfo,
            'platGameList'=>$platGameList,
            'hotZtList'=>$hotZtList,
            'classify'=>$gameInfo['classify'],
            'gameAboutVersion'=>$gameAboutVersion ?? [],
            'tagInfo'=>$tagInfo ?? [],
            'tagsLikeData'=>$tagsLikeData ?? [],
            'aboutGameList'=>$aboutGameList ?? [],
            'goodGameList'=>$goodGameList,
            'addadmin'=> $gameInfo['addadmin'],
            'addtime'=>$gameInfo['addtime'],
            'uptime'=>$gameInfo['uptime'],
            'tdk_detail'=>1,
        ];

        return $info;
    }

    protected function dealSoftDetail($union_id,&$info){

        $types = $this->request->uri->getSegment(1);

        $info = [];

        $gameListModel = new AppListModel();
        $gameService   = new AppService();
        $tagService    = new TagService();
        $adService     = new AdService();

        $gameInfo      = $gameListModel->getGameInfoByUnionId($union_id, true, true, true);

        if (empty($gameInfo)) {
            $this->show_404();
        }

        $tdk = [
            'title'=>$gameInfo['title'],
            'keywords'=>$gameInfo['keywords'],
            'description'=>$gameInfo['description']
        ];
        $gameInfo['star'] = number_format(mt_rand(70,99) /10,1);
        $gameInfo['downDetail'] = '/app/'.$union_id.'/download';

        //更新浏览次数
        $time = date('YmdH0000');
        $this->redis->HINCRBY(env('SITE_ID', 0).":".CacheHelper::APP_VIEW_DETAIL.":".$time.":mview",$gameInfo['id'],1);
        $this->redis->HINCRBY(env('SITE_ID', 0).":".CacheHelper::APP_VIEW_DETAIL.":".$time.":wview",$gameInfo['id'],1);
        $this->redis->HINCRBY(env('SITE_ID', 0).":".CacheHelper::APP_VIEW_DETAIL.":".$time.":twview",$gameInfo['id'],1);

        $gfield = 'bgl.id,bgl.title,bgl.shortname,bgp.and_url,bgl.icon,bgl.type,bgl.classify,bgl.uptime,bgp.and_size as size,bgp.and_unit as unit,bgl.tags,bgl.name,bgl.comment,bgl.union_id';
        $tfield = 'id,name,full_name,catalog,type,img,gameid,uptime';

        //相关专题 取id最小
        $tagsLikeData = [];
        if(!empty($gameInfo['tags'])){
            $likeTagWhere = 'status = 1 and id in ('.$gameInfo['tags'] . ')';
            $tagFields    = 'id,name,title,catalog,img,full_name,img,type';
            $tagsLikeData = $tagService->getNewsList($tagFields, 1, $likeTagWhere,0,false,0,'id asc');
        }
        $tagsLikeData = $tagsLikeData ? $tagsLikeData[0] : [];
        if(!empty($tagsLikeData)){
            $where = "status = 1 and state=1 and tagid={$tagsLikeData['id']}";
            $tagsLikeData['gamecount'] = $gameListModel->getCountByTag($where);
        }

        //开发者其他游戏
        $platGameList = $gameService->getAppList($gfield,6,'bgl.uptime desc'," and platid = {$gameInfo['platid']} and bgl.id != {$gameInfo['id']}");
        //热门标签
        $twhere = 'status = 1 and state=2 and type = 2' ;
        $hotZtList = $tagService->getNewsList($tfield,9,$twhere);

        //相关版本 主库相关
        if ($gameInfo['gameid']) {
            $aboutWhere = " and bgl.gameid = " .$gameInfo['gameid'];
            $aboutField = $gfield . ',bgp.and_ver';
            $gameAboutVersion = $gameService->getAppList($aboutField, 10, 'bgl.uptime desc', $aboutWhere);
        }

        //类似游戏
        if(!empty($tagsLikeData)){
            $aboutGameList = $gameService->getListByTag($gfield,6,'uptime desc',"  and bgl.id != {$gameInfo['id']} and t.tagid = {$tagsLikeData['id']}");
        }

        //好游安利
        $goodGameList = $adService->getAdvGameOrAppPackList(MEnum::M_APP_DETAIL_GOOD);

        $info = [
            'nav'=> 3.1,
            'tdk'=>$tdk,
            'gInfo'=>$gameInfo,
            'platGameList'=>$platGameList,
            'hotZtList'=>$hotZtList,
            'classify'=>$gameInfo['classify'],
            'gameAboutVersion'=>$gameAboutVersion ?? [],
            'tagInfo'=>$tagInfo ?? [],
            'tagsLikeData'=>$tagsLikeData ?? [],
            'aboutGameList'=>$aboutGameList ?? [],
            'goodGameList'=>$goodGameList,
            'addadmin'=> $gameInfo['addadmin'],
            'addtime'=>$gameInfo['addtime'],
            'uptime'=>$gameInfo['uptime'],
            'tdk_detail'=>1,
        ];

        return $info;
    }

    /**
     * game detail download
     */
    public function gameDownload($union_id = '')
    {
        $types = $this->request->uri->getSegment(1);

        $gameListModel = new GameListModel();
        $gameService   = new GameService();
        $tagService    = new TagService();
        $adService     = new AdService();

        $gameInfo      = $gameListModel->getGameInfoByUnionId($union_id, true, true, true);

        if (empty($gameInfo)) {
            $this->show_404();
        }

        $tdk = [
            'title'=>"Download {$gameInfo['name']} lastest {$gameInfo['game_pack']['and_ver']} Android APK",
            'keywords'=>"{$gameInfo['name']} download, {$gameInfo['name']} android download",
            'description'=>"{$gameInfo['name']}, download, android, apk, {$gameInfo['name']} download, {$gameInfo['name']} android, {$gameInfo['name']} {$gameInfo['game_pack']['and_ver']}"
        ];
        $gameInfo['star'] = number_format(mt_rand(70,99) /10,1);

        $gfield = 'bgl.id,bgl.title,bgl.shortname,bgp.and_url,bgl.icon,bgl.type,bgl.classify,bgl.uptime,bgp.and_size as size,bgp.and_unit as unit,bgl.tags,bgl.name,bgl.comment,bgl.union_id';
        $tfield = 'id,name,full_name,catalog,type,img,gameid,uptime';

        //相关专题 取id最小
        $tagsLikeData = [];
        if(!empty($gameInfo['tags'])){
            $likeTagWhere = 'status = 1 and id in ('.$gameInfo['tags'] . ')';
            $tagFields    = 'id,name,title,catalog,img,full_name,img,type';
            $tagsLikeData = $tagService->getNewsList($tagFields, 1, $likeTagWhere,0,false,0,'id asc');
        }
        $tagsLikeData = $tagsLikeData ? $tagsLikeData[0] : [];
        if(!empty($tagsLikeData)){
            $where = "status = 1 and state=1 and tagid={$tagsLikeData['id']}";
            $tagsLikeData['gamecount'] = $gameListModel->getCountByTag($where);
        }

        //开发者其他游戏
        $platGameList = $gameService->getGameList($gfield,6,'bgl.uptime desc'," and platid = {$gameInfo['platid']} and bgl.id != {$gameInfo['id']}");
        //热门标签
        $twhere = 'status = 1 and state=2 and type = 1' ;
        $hotZtList = $tagService->getNewsList($tfield,9,$twhere);

        //相关版本 主库相关
        if ($gameInfo['gameid']) {
            $aboutWhere = " and bgl.gameid = " .$gameInfo['gameid'];
            $aboutField = $gfield . ',bgp.and_ver';
            $gameAboutVersion = $gameService->getGameList($aboutField, 10, 'bgl.uptime desc', $aboutWhere);
        }

        //类似游戏
        if(!empty($tagsLikeData)){
            $aboutGameList = $gameService->getListByTag($gfield,6,'uptime desc',"  and bgl.id != {$gameInfo['id']} and t.tagid = {$tagsLikeData['id']}");
        }

        //好游安利
        $goodGameList = $adService->getAdvGameOrAppPackList(MEnum::M_GAME_DETAIL_GOOD);

        $downGameList = $gameService->getGameList($gfield,5,'wview desc,uptime desc');

        $info = [
            'nav'=> 2.1,
            'tdk'=>$tdk,
            'gInfo'=>$gameInfo,
            'platGameList'=>$platGameList,
            'hotZtList'=>$hotZtList,
            'classify'=>$gameInfo['classify'],
            'gameAboutVersion'=>$gameAboutVersion ?? [],
            'tagInfo'=>$tagInfo ?? [],
            'tagsLikeData'=>$tagsLikeData ?? [],
            'aboutGameList'=>$aboutGameList ?? [],
            'goodGameList'=>$goodGameList,
            'addadmin'=> $gameInfo['addadmin'],
            'addtime'=>$gameInfo['addtime'],
            'uptime'=>$gameInfo['uptime'],
            'tdk_detail'=>1,
            'downGameList'=>$downGameList,
        ];
        return view("/Statics/M/game/download.html", $info);
    }

    /**
     * soft detail download
     */
    public function softDownload($union_id = ''){
        $gameListModel = new AppListModel();
        $gameService   = new AppService();
        $tagService    = new TagService();

        $gameInfo      = $gameListModel->getGameInfoByUnionId($union_id, true, true, true);

        if (empty($gameInfo)) {
            $this->show_404();
        }

        $tdk = [
            'title'=>"Download {$gameInfo['name']} lastest {$gameInfo['game_pack']['and_ver']} Android APK",
            'keywords'=>"{$gameInfo['name']} download, {$gameInfo['name']} android download",
            'description'=>"{$gameInfo['name']}, download, android, apk, {$gameInfo['name']} download, {$gameInfo['name']} android, {$gameInfo['name']} {$gameInfo['game_pack']['and_ver']}"
        ];
        $gameInfo['star'] = number_format(mt_rand(70,99) /10,1);


        $gfield = 'bgl.id,bgl.title,bgl.shortname,bgp.and_url,bgl.icon,bgl.type,bgl.classify,bgl.uptime,bgp.and_size as size,bgp.and_unit as unit,bgl.tags,bgl.name,bgl.comment,bgl.union_id';
        $tfield = 'id,name,full_name,catalog,type,img,gameid,uptime';

        //相关专题 取id最小
        $tagsLikeData = [];
        if(!empty($gameInfo['tags'])){
            $likeTagWhere = 'status = 1 and id in ('.$gameInfo['tags'] . ')';
            $tagFields    = 'id,name,title,catalog,img,full_name,img,type';
            $tagsLikeData = $tagService->getNewsList($tagFields, 1, $likeTagWhere,0,false,0,'id asc');
        }
        $tagsLikeData = $tagsLikeData ? $tagsLikeData[0] : [];
        if(!empty($tagsLikeData)){
            $where = "status = 1 and state=1 and tagid={$tagsLikeData['id']}";
            $tagsLikeData['gamecount'] = $gameListModel->getCountByTag($where);
        }

        //开发者其他游戏
        $platGameList = $gameService->getAppList($gfield,6,'bgl.uptime desc'," and platid = {$gameInfo['platid']} and bgl.id != {$gameInfo['id']}");
        //热门标签
        $twhere = 'status = 1 and state=2 and type = 2' ;
        $hotZtList = $tagService->getNewsList($tfield,9,$twhere);

        //相关版本 主库相关
        if ($gameInfo['gameid']) {
            $aboutWhere = " and bgl.gameid = " .$gameInfo['gameid'];
            $aboutField = $gfield . ',bgp.and_ver';
            $gameAboutVersion = $gameService->getAppList($aboutField, 10, 'bgl.uptime desc', $aboutWhere);
        }

        //类似游戏
        if(!empty($tagsLikeData)){
            $aboutGameList = $gameService->getListByTag($gfield,6,'uptime desc',"  and bgl.id != {$gameInfo['id']} and t.tagid = {$tagsLikeData['id']}");
        }

        $info = [
            'nav'=> 3.1,
            'tdk'=>$tdk,
            'gInfo'=>$gameInfo,
            'platGameList'=>$platGameList,
            'hotZtList'=>$hotZtList,
            'classify'=>$gameInfo['classify'],
            'gameAboutVersion'=>$gameAboutVersion ?? [],
            'tagInfo'=>$tagInfo ?? [],
            'tagsLikeData'=>$tagsLikeData ?? [],
            'aboutGameList'=>$aboutGameList ?? [],
            'addadmin'=> $gameInfo['addadmin'],
            'addtime'=>$gameInfo['addtime'],
            'uptime'=>$gameInfo['uptime'],
            'tdk_detail'=>1,
        ];
        return view("/Statics/M/game/download.html", $info);
    }

}
