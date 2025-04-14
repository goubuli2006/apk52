<?php

namespace App\Controllers\Mobile;


use App\Services\AdService;
use App\Services\GameService;

class Errors extends BaseController
{
    public function show404()
    {
        $gameService = new GameService();
        $gfields = "bgl.id,bgl.name,bgl.icon,bgl.type,bgl.classify,bgl.shortname,bgl.game_score,bgl.uptime,bgl.gameid,bgp.and_url as downurl,bgl.tags,bgl.union_id";
        $gameList = $gameService->getGameList($gfields, 8, 'uptime desc');
        $info['gameList']   = $gameList;
        $info['nav'] = 0;
        return view("/Statics/M/errors/404.html",$info);
    }

}
