<?php

namespace App\Controllers\Pc;

use App\Models\AppListModel;
use App\Models\GameListModel;
use App\Models\TagModel;
use App\Services\PloyCategoryService;
use App\Services\TagService;
use App\Services\GameService;
use App\Enum\PolyCatEnum;
use App\Services\AppService;


class Topic extends BaseController
{
    public function index()
    {
        $info = [];
        $this->dealIndexInfo($info);
        return view("/Statics/Pc/topic/zt.html", $info);
    }
    public function detail()
    {
        $info = [];
        $this->dealDetailInfo($info);
        return view("/Statics/Pc/topic/ztdetail.html", $info);
    }

    protected function dealIndexInfo(&$info)
    {
        $limit = 10;
        $ployCateInfo = [];
        $twhere = 'status = 1';

        $params = [
            'page' => 1,
            'current_type' => '',
            'orderNum' => 1
        ];

        $ploycateService = new PloyCategoryService();
        $tagModel        = new TagModel();
        $tagService      = new TagService();
        $pager           = \Config\Services::pager();

        $type = 0;
        $ztParam = $this->request->uri->getSegment(2);
        $baseUrl = 'subclass/';  //固定  不使用聚合类，因为路由固定 一改具改
        if ($ztParam) {
            if (is_numeric($ztParam)) {
                $params['page'] = $ztParam;
            } else {
                $pageS = $this->request->uri->getSegment(3); // heji/yxhj/1
                if ($pageS && !is_numeric($pageS)) {
                    list($params['orderNum'], $params['page']) = explode('-', $pageS);
                } else {
                    $params['page'] = $pageS ? : $params['page'];
                }
                //游戏、软件专题
                if (in_array($ztParam, ['game', 'app'])) {
                    $params['current_type'] = $ztParam;
                    if ($ztParam == 'game') {
                        $ployCateInfo = $ploycateService->getCategoryById(PolyCatEnum::APP_POLY_CAT_TAGS_GAME);
                        $baseUrl = "subclass/game/";
                        $twhere .= ' and type = 1';
                    }
                    if ($ztParam == 'app') {
                        $ployCateInfo = $ploycateService->getCategoryById(PolyCatEnum::APP_POLY_CAT_TAGS_APP);
                        $baseUrl = "subclass/app/";
                        $twhere .= ' and type = 2';
                    }
                } else {
                     $this->show_404();
                }
            }
        }
        //聚合类
        if (empty($ployCateInfo)) {
            $ployCateInfo = $ploycateService->getCategoryById(PolyCatEnum::APP_POLY_CAT_TAGS);
        }
        
        $offset = ($params['page'] - 1) * $limit;

        $tdk = [
            'title'=> $ployCateInfo['seo_title'],
            'keywords'=> $ployCateInfo['seo_keywords'],
            'description'=> $ployCateInfo['seo_description'],
        ];

        $pager->setPath($baseUrl);

        $tagFields = 'id,name,title,catalog,img,full_name,img,type,introduce,game_num,uptime,state,status,is404';
        $tagsLists = $tagService->getNewsList($tagFields, $limit, $twhere, $offset,true);
        $totalRows = $tagModel->getTagListCount($twhere);
        $links     = $pager->makeLinks($params['page'], $limit, $totalRows, 'default_all');

        $hotZtList = $tagService->getNewsList($tagFields,4,$twhere.' and state=2');

        $info = [
            'tdk'=>$tdk,
            'nav'=>5,
            'links'=>$links,
            'tagsLists'=>$tagsLists,
            'hotZtList'=>$hotZtList,
            'current_type'=>$params['current_type'],
        ];

    }

    protected function dealDetailInfo(&$info)
    {
        $page = 1;
        $limit = 12;
        $params = $this->request->uri->getSegment(2);
        $pages = $this->request->uri->getSegment(3);
        if ($pages && is_numeric($pages)){
            $page = $pages;
        }
        $offset = ($page - 1) * $limit;
        
        $tagModel        = new TagModel();
        $tagService      = new TagService();
        $ploycateService = new PloyCategoryService();
        $gameService      = new GameService();
        $gameListModel   = new GameListModel();
        $appListModel    = new AppListModel();
        $appService      = new AppService();
        $pager           = \Config\Services::pager();

        $ployCateInfo = $ploycateService->getCacheCategofyByIdOrCatalog(PolyCatEnum::APP_POLY_CAT_TAGS);
        $baseUrl = $ployCateInfo['catalog'] . "/" . $params ."/";

        //详情
        $where     = ['status' => 1, 'catalog' => $params];
        $tagfields = 'id,name,catalog,full_name,catalog,type,title,keyword,description,img,introduce,addtime,uptime,addadmin,game_num,tags,remark,remark_tags,gameid,is404';
        $tagInfo   = $tagModel->getOneInfoByWhere($where, $tagfields);
        if (empty($tagInfo)) {
            $this->show_404();
        }

        $tagInfo = $tagService->getTagFormatData([$tagInfo],false)[0];

        $gfield = 'bgl.id,bgl.title,bgl.icon,bgl.type,bgl.classify,bgl.uptime,bgp.and_size as size,bgp.and_unit as unit,bgl.tags,bgl.name,bgl.comment,bgp.and_url as downurl,bgl.union_id';
        $orderBy = 'uptime desc';
        $tableWhere = "status = 1 and state = 1 and tagid={$tagInfo['id']}";
        if ($tagInfo['type'] == 1) {
            $tagInfo['gameList'] = $gameService->getListByTag($gfield, $limit, $orderBy, " and tagid={$tagInfo['id']}", $offset);
            $tagInfo['gamecount'] = $gameListModel->getCountByTag($tableWhere);
        } else {
            $tagInfo['gameList']  = $appService->getListByTag($gfield, $limit, $orderBy, " and tagid={$tagInfo['id']}", $offset);
            $tagInfo['gamecount'] = $appListModel->getCountByTag($tableWhere);
        }

        $pager->setPath($baseUrl);
        $links = $pager->makeLinks($page, $limit,$tagInfo['gamecount'], 'default_all');

        //关联标签
        $twhere = "status = 1 and id in ({$tagInfo['tags']})";
        if(!empty($tagInfo['tags'])){
            $relationTagList = $tagService->getNewsList($tagfields,0,$twhere);
        }

        //热门专题推荐
        $twhere = 'status = 1 and state=2 and type = '.$tagInfo['type'] ;
        $hotZtList = $tagService->getNewsList($tagfields,3,$twhere);

        $tdk = [
            'title'=> $ployCateInfo['seo_title'],
            'keywords'=> $ployCateInfo['seo_keywords'],
            'description'=> $ployCateInfo['seo_description'],
        ];

        $info = [
            'tdk'=>$tdk,
            'nav'=>5.1,
            'tdk_detail'=>1,
            'tagInfo'=>$tagInfo,
            'addtime'=>$tagInfo['addtime'],
            'uptime'=>$tagInfo['uptime'],
            'addadmin'=>$tagInfo['addadmin'],
            'links'=>$links,
            'hotZtList'=>$hotZtList,
            'relationTagList'=>$relationTagList??[],
            'type'=>$tagInfo['type']
        ];



    }


}
