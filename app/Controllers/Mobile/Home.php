<?php

namespace App\Controllers\Mobile;

use App\Enum\MEnum;
use App\Enum\PolyCatEnum;
use App\Services\AdService;
use App\Services\AppService;
use App\Services\GameService;
use App\Services\PloyCategoryService;
use App\Services\TagService;

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
        $ployCateService = new PloyCategoryService();

        //轮播图
        $lbList = $adService->getAdvert(MEnum::M_AD_HOME_LB_INDEX);

        $hotSpecial = $adService->getAdvert(MEnum::M_AD_HOME_HOT_SPECIAL);
        $hotNews = $adService->getAdvert(MEnum::M_AD_HOME_HOT_NEWS);

        $topGameList = $adService->getAdvGameOrAppPackList(MEnum::M_AD_HOME_TOP_GAME);

        $topAppList = $adService->getAdvGameOrAppPackList(MEnum::M_AD_HOME_TOP_APP);
        $recomGameList = $adService->getAdvGameOrAppPackList(MEnum::M_AD_HOME_RECOMMEND);
        $updateGameList = $adService->getAdvertIdByGameList(MEnum::M_AD_HOME_NEW_UPDATE);

        $gfield = "bgl.id,bgl.name,bgl.type,bgl.classify,bgl.uptime,bgl.union_id,bgl.icon";
        $gfield .= $this->isios == 1 ? ',bgp.ios_size as size,bgp.ios_unit as unit,bgp.ios_url as downurl' : ',bgp.and_size as size,bgp.and_unit as unit,bgp.and_url as downurl';

        //手机游戏全部
        $gameNewMobileList = $gameService->getGameList($gfield, 12);

        $tfield = 'id,name,full_name,catalog,type,img,gameid,uptime';
        $gameZtList = $tagService->getTagList(1,$tfield, 4);

        $freeGameList = $adService->getAdvGameOrAppPackList(MEnum::M_AD_HOME_HOT_GAME_FREE);
        $freeGameList = array_chunk($freeGameList, 3);

        $expeGameList = $adService->getAdvGameOrAppPackList(MEnum::M_AD_HOME_HOT_GAME_EXPE);
        $expeGameList = array_chunk($expeGameList, 3);

        $downGameList = $gameService->getGameList($gfield,9,'wview desc,uptime desc');
        $downGameList = array_chunk($downGameList, 3);

        //手机应用全部
        $appNewMobileList = $appService->getAppList($gfield, 12);

        $appZtList = $tagService->getTagList(2,$tfield, 4);

        $freeAppList = $adService->getAdvGameOrAppPackList(MEnum::M_AD_HOME_HOT_APP_FREE);
        $freeAppList = array_chunk($freeAppList, 3);

        $expeAppList = $adService->getAdvGameOrAppPackList(MEnum::M_AD_HOME_HOT_APP_EXPE);
        $expeAppList = array_chunk($expeAppList, 3);

        $downAppList = $appService->getAppList($gfield,9,'wview desc,uptime desc');
        $downAppList = array_chunk($downAppList, 3);

        $ployCateInfo = $ployCateService->getCategoryById(PolyCatEnum::APP_POLY_CAT_INDEX);
        $tdk = [
            'title'=> $ployCateInfo['seo_title'],
            'keywords'=> $ployCateInfo['seo_keywords'],
            'description'=> $ployCateInfo['seo_description'],
        ];

        $data = [
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
        return view("/Statics/M/index.html", $data);

    }

}