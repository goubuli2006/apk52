<?php

namespace App\Controllers\Pc;

use App\Enum\CategoryEnum;
use App\Enum\PolyCatEnum;
use App\Models\CategoryModel;
use App\Services\PloyCategoryService;
use App\Services\TagService;
use App\Services\AdService;
use App\Services\AppService;
use App\Enum\PcEnum;
use App\Services\GameService;

class Home extends BaseController
{

    /**
     * 首页
     */
    public function index()
    {
        $adService       = new AdService();
        $gameService     = new GameService();
        $appService      = new AppService();
        $tagService      = new TagService();
        $categoryModel   = new CategoryModel();
        $ployCateService = new PloyCategoryService();

        //轮播图
        $lbList = $adService->getAdvert(PcEnum::PC_AD_HOME_LB_INDEX);

        $hotSpecial = $adService->getAdvert(PcEnum::PC_AD_HOME_HOT_SPECIAL);
        $hotNews = $adService->getAdvert(PcEnum::PC_AD_HOME_HOT_NEWS);

        $topGameList = $adService->getAdvGameOrAppPackList(PcEnum::PC_AD_HOME_TOP_GAME);
        $topAppList = $adService->getAdvGameOrAppPackList(PcEnum::PC_AD_HOME_TOP_APP);
        $recomGameList = $adService->getAdvGameOrAppPackList(PcEnum::PC_AD_HOME_RECOMMEND);

        $updateGameList = $adService->getAdvertIdByGameList(PcEnum::PC_AD_HOME_NEW_UPDATE);

        $gfield = "bgl.id,bgl.name,bgl.type,bgl.classify,bgl.uptime,bgl.union_id,bgl.icon,bgp.and_size as size,bgp.and_unit as unit,bgp.and_ver,mview";

        //手机游戏
        $categoryList = $categoryModel->getTableLists(['pid' => CategoryEnum::GAME_CATE_PID]);

        foreach ($categoryList as $v) {
            $typeWhere = ' and type ='. $v['id'];
            $gameNewMobileTypeList = $gameService->getGameList($gfield, 16, 'bgl.uptime desc', $typeWhere);
            $gameNewMobileTypeLists[$v['name']] = $gameNewMobileTypeList;
        }

        //手机游戏全部 推荐type
        $gameNewMobileList = $gameService->getGameList($gfield, 16, 'bgl.uptime desc', '  and recom = 2');

        $tfield = 'id,name,full_name,catalog,type,img,gameid,uptime';
        $gameZtList = $tagService->getTagList(1,$tfield, 4);

        $freeGameList = $adService->getAdvGameOrAppPackList(PcEnum::PC_AD_HOME_HOT_GAME_FREE);
        $freeGameList = array_chunk($freeGameList, 3);

        $expeGameList = $adService->getAdvGameOrAppPackList(PcEnum::PC_AD_HOME_HOT_GAME_EXPE);
        $expeGameList = array_chunk($expeGameList, 3);

        $downGameList = $gameService->getGameList($gfield,9,'wview desc,uptime desc');
        $downGameList = array_chunk($downGameList, 3);

        $appCategoryList = $categoryModel->getTableLists(['pid' => CategoryEnum::SOFT_CATE_PID]);
        foreach ($appCategoryList as $v) {
            $typeWhere = ' and type ='. $v['id'];
            $appNewMobileTypeList = $appService->getAppList($gfield, 16, 'bgl.uptime desc', $typeWhere);
            $appNewMobileTypeLists[$v['name']] = $appNewMobileTypeList;
        }
        //手机应用全部 推荐type
        $appNewMobileList = $appService->getAppList($gfield, 16, 'bgl.uptime desc', '  and recom = 2');

        $appZtList = $tagService->getTagList(2,$tfield, 4);

        $freeAppList = $adService->getAdvGameOrAppPackList(PcEnum::PC_AD_HOME_HOT_APP_FREE);
        $freeAppList = array_chunk($freeAppList, 3);

        $expeAppList = $adService->getAdvGameOrAppPackList(PcEnum::PC_AD_HOME_HOT_APP_EXPE);
        $expeAppList = array_chunk($expeAppList, 3);

        $downAppList = $appService->getAppList($gfield,9,'wview desc,uptime desc');
        $downAppList = array_chunk($downAppList, 3);

        $ployCateInfo = $ployCateService->getCategoryById(PolyCatEnum::APP_POLY_CAT_INDEX);
        $tdk = [
            'title'=> $ployCateInfo['seo_title'],
            'keywords'=> $ployCateInfo['seo_keywords'],
            'description'=> $ployCateInfo['seo_description'],
        ];

        $info = [
            'nav'=>1,
            'tdk'=>$tdk,
            'lbList'=>$lbList,
            'hotSpecial'=>$hotSpecial,
            'hotNews'=>$hotNews,
            'topGameList'=>$topGameList,
            'topAppList'=>$topAppList,
            'recomGameList'=>$recomGameList,
            'updateGameList'=>$updateGameList,
            'gameNewMobileTypeLists'=>$gameNewMobileTypeLists ?? [],
            'gameNewMobileList'=>$gameNewMobileList,
            'gameZtList'=>$gameZtList,
            'freeGameList'=>$freeGameList,
            'expeGameList'=>$expeGameList,
            'downGameList'=>$downGameList,
            'appNewMobileTypeLists'=>$appNewMobileTypeLists ?? [],
            'appNewMobileList'=>$appNewMobileList,
            'appZtList'=>$appZtList,
            'freeAppList'=>$freeAppList,
            'expeAppList'=>$expeAppList,
            'downAppList'=>$downAppList,
        ];
        return view("/Statics/Pc/home/index.html", $info);
    }

}
