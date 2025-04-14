<?php

namespace App\Models;

use App\Helpers\VeImage;
use App\Services\AppService;
use App\Services\GameService;
use App\Services\TagService;
use Config\Custom;

class GameListModel extends BaseModel
{

    protected $table = 'game_list';

    /**
     * @function getNewGameList:
     */
    public function getNewGameList($where = [], $whereIn = [], $field = '*', $limit = 0, $offset = 0, $order = '', $group = '')
    {

        $query = $this->db->table('game_list as bgl')
            ->join('game_pack as bgp', 'bgl.id=bgp.gameid', 'left')
            ->where($where);

        if (!empty($whereIn)) {
            $query = $query->whereIn('bgl.id', $whereIn);
        }
        return   $query->select($field)
            ->limit($limit, $offset)
            ->orderBy($order)
            ->groupBy($group)
            ->get()
            ->getResultArray();
    }

    /**
     * @function gameNewCount:
     */
    public function gameNewCount($where = array())
    {

        return $this->db->table('game_list as bgl')
            ->join('game_pack as bgp', 'bgl.id=bgp.gameid')
            ->where($where)
            ->select('COUNT(*) AS num')
            ->countAllResults();
    }

    /**
     * 获取详细信息
     * getShortImg 是否获取截图
     * getGamePack 是否获取软件包
     */
    public function getGameInfoById($id, $getShortImg = false, $getGamePack = false, $getTypeName = false)
    {
        $gameInfo = [];
        if (!$id) return $gameInfo;
        $gamePackModel = new GamePackModel();
        $gameImgModel  = new GameImgModel();
        $veImageHelper = new VeImage();
        $PlatModel     = new PlatModel();
        $gfield = 'id,name,gameid,shortname,title,keywords,description,type,icon,status,introduce,tags,platid,uptime,classify,game_score,addtime,video,video_cover,addadmin,state,comment,introduce_log,history_introduce_log,union_id,isshow,privacy,version_name,icp_code,common_id';
        $gameInfo = $this->getOneInfoByWhere(['id' => $id, 'status' => 1, "state" => 1], $gfield);
        if (empty($gameInfo)) return $gameInfo;

        //截图信息
        if ($getShortImg) {
            $gameShortImg = $gameImgModel->getTableList(['gameid' => $id, 'status' => 1], "*", 8);
            foreach ($gameShortImg as &$val) {
                $val['path'] = $veImageHelper->dealVeImage($val['path']);
            }
            $gameInfo['game_short_img'] = $gameShortImg;
        }

        //视频后缀处理
        if ($gameInfo['video']) {
            $suffix = explode('.', $gameInfo['video']);
            $gameInfo['video_suffix'] = strtolower($suffix[count($suffix) - 1]);
        } else {
            $gameInfo['video_suffix'] = "";
        }

        $gameInfo['video_cover'] = $veImageHelper->dealVeImage($gameInfo['video_cover']);

        //安装包信息
        $game_pack = '';
        if ($getGamePack) {
            $pack_field = "id,gameid,and_url,and_size,and_ver,and_unit,ios_ver,ios_size,ios_url,ios_unit,pc_url,and_md5,and_pkgname";
            $game_pack = $gamePackModel->getOneInfoByWhere(['gameid' => $id], $pack_field);
            if ($game_pack) {
                $packUnit = Custom::getPackUnit();
                $game_pack['size_format'] = $game_pack['and_size'] ? $game_pack['and_size'] . $packUnit[$game_pack['and_unit']] : '0M';
                $game_pack['ios_size_format'] = $game_pack['ios_size'] ? $game_pack['ios_size'] . (isset($packUnit[$game_pack['ios_unit']])?$packUnit[$game_pack['ios_unit']]:'M') : '0M';
            }

        }
        $gameInfo['game_pack'] = $game_pack;

        $gameInfo['icon'] = $veImageHelper->dealVeImage($gameInfo['icon']);

        //厂商信息
        $platName = $platPrivacy ='';
        if ($gameInfo['platid']) {
            $platInfo = $PlatModel->getInfoById($gameInfo['platid'],'name,privacy');
            $platName = !empty($platInfo) ? $platInfo['name'] : '';
            $platPrivacy = !empty($platInfo) ? $platInfo['privacy'] : '';
        }
        
        $gameInfo['plat_name'] = $platName ? $platName : '暂无资料';
        $gameInfo['plat_privacy'] = $platPrivacy ? $platPrivacy : '';

        //类型
        $typeName = $typeHref = '';
        if ($getTypeName) {
            $gameService = new AppService();
            $typeInfo = $gameService->getTypeInfoUrl($gameInfo['type'], $gameInfo['classify']);
            if (!empty($typeInfo)) {
                $typeName = $typeInfo['name'];
                $typeHref = $typeInfo['href'];
            }
        }
        $gameInfo['type_name'] = $typeName;
        $gameInfo['type_info'] = $typeHref;

        //专题信息 关联标签
        $tagsInfo = [];
        $tagService = new TagService();
        if ($gameInfo['tags']) {
            $tagIdWhere = " status = 1 and id in ({$gameInfo['tags']})";
            $tagsInfo = $tagService->getTagListByIdIn($tagIdWhere, 'id,name,game_num as gamecount,uptime,img,introduce,catalog,full_name,status', 10);
        }
        $gameInfo['tagsInfo'] = $tagsInfo;

        //关联主库
        $gfield = 'bgl.id,bgl.type,bgl.classify,bgl.union_id,bg.id as mid';
        $gameService = new GameService();
        $gameMasterInfo = $gameService->getMasterOneGameInfo($gfield,'bg.id='.$gameInfo['gameid']);
        if(!empty($gameMasterInfo)){
            $gameMasterInfo = $gameService->getFormatData([$gameMasterInfo]);
            $gameInfo['master_href'] = $gameMasterInfo[0]['master_href'];
        }
        return $gameInfo;
    }

    /* 获取详细信息
    * getShortImg 是否获取截图
    * getGamePack 是否获取软件包
    */
    public function getGameInfoByUnionId($union_id, $getShortImg = false, $getGamePack = false, $getTypeName = false)
    {
        $gameInfo = [];
        if (!$union_id) return $gameInfo;
        $gamePackModel = new GamePackModel();
        $gameImgModel  = new GameImgModel();
        $veImageHelper = new VeImage();
        $PlatModel = new PlatModel();

        $gfield = 'id,name,gameid,shortname,title,keywords,description,type,icon,status,introduce,tags,platid,uptime,classify,game_score,addtime,video,video_cover,addadmin,state,comment,introduce_log,history_introduce_log,union_id,isshow';
        $gameInfo = $this->getOneInfoByWhere(['union_id' => $union_id, 'status' => 1, "state" => 1], $gfield);
        if (empty($gameInfo)) return $gameInfo;
        $gameInfo['star'] = mt_rand(4, 5);

        $id = $gameInfo['id'];
        //截图信息
        if ($getShortImg) {
            $gameShortImg = $gameImgModel->getTableList(['gameid' => $id, 'status' => 1], "*", 8);
            foreach ($gameShortImg as &$val) {
                $val['path'] = $veImageHelper->dealVeImage($val['path']);
            }
            $gameInfo['game_short_img'] = $gameShortImg;
        }

        //视频后缀处理
        if ($gameInfo['video']) {
            $suffix = explode('.', $gameInfo['video']);
            $gameInfo['video_suffix'] = strtolower($suffix[count($suffix) - 1]);
        } else {
            $gameInfo['video_suffix'] = "";
        }

        $gameInfo['video_cover'] = $veImageHelper->dealVeImage($gameInfo['video_cover']);

        //安装包信息
        $game_pack = '';
        if ($getGamePack) {
            $pack_field = "id,gameid,and_url,and_size,and_ver,and_unit,ios_ver,ios_size,ios_url,ios_unit,pc_url,and_md5,and_pkgname";
            $game_pack = $gamePackModel->getOneInfoByWhere(['gameid' => $id], $pack_field);
            if ($game_pack) {
                $packUnit = Custom::getPackUnit();
                $game_pack['size_format'] = $game_pack['and_size'] ? $game_pack['and_size'] . $packUnit[$game_pack['and_unit']] : '0M';
            }

        }
        $gameInfo['game_pack'] = $game_pack;

        $gameInfo['icon'] = $veImageHelper->dealVeImage($gameInfo['icon']);

        //厂商信息
        $platName = $platPrivacy ='';
        if ($gameInfo['platid']) {
            $platInfo = $PlatModel->getInfoById($gameInfo['platid'],'name,privacy');
            $platName = !empty($platInfo) ? $platInfo['name'] : '';
            $platPrivacy = !empty($platInfo) ? $platInfo['privacy'] : '';
        }

        $gameInfo['plat_name'] = $platName ? $platName : '暂无资料';
        $gameInfo['plat_privacy'] = $platPrivacy ? $platPrivacy : '';

        //类型
        $typeName = $typeHref = '';
        if ($getTypeName) {
            $gameService = new AppService();
            $typeInfo = $gameService->getTypeInfoUrl($gameInfo['type'], $gameInfo['classify']);
            if (!empty($typeInfo)) {
                $typeName = $typeInfo['name'];
                $typeHref = $typeInfo['href'];
            }
        }
        $gameInfo['type_name'] = $typeName;
        $gameInfo['type_info'] = $typeHref;

        //专题信息 关联标签
        $tagsInfo = [];
        $tagService = new TagService();
        if ($gameInfo['tags']) {
            $tagIdWhere = "status = 1 and id in ({$gameInfo['tags']})";
            $tagsInfo = $tagService->getTagListByIdIn($tagIdWhere, 'id,name,game_num as gamecount,uptime,img,introduce,catalog,full_name,status', 10);
        }
        $gameInfo['tagsInfo'] = $tagsInfo;

        return $gameInfo;
    }


    public function updateBatchData($data, $where = [])
    {
        if (empty($where)) {
            return false;
        }
        return $this->db->table($this->table)->update($data, $where);
    }

    public function setGameView($data, $field){
        foreach ($data as $key=>$val){
            $result = $this->db->table($this->table)->where('id', $val['id'])
                ->set("$field", "{$field} +".$val[$field],FALSE)->update();
        }
        return $result;
    }

    /**
     * 获取主库关联游戏列表
     *
     * @param string $where
     * @param string $field
     * @param integer $limit
     * @param integer $offset
     * @param string $order
     * @param string $group
     * @return array
     */
    public function getMasterGameList(string $where = "", $field = ['*'], $limit = 0, $offset = 0, $order = '', $group = 'bg.id')
    {
        return $this->db->table('game as bg')
            ->join('game_list as bgl', 'bg.id=bgl.gameid')
            ->join('similar_game as sg', 'bg.id = sg.fid')
            ->select($field.',count(sg.fid) as snums')
            ->where("bg.name = bgl.name and bg.status = 1 and bg.state = 1 and bg.show_down = 1 and sg.classify = 1 ")
            ->where($where?:[])
            ->groupBy($group)
            ->having('snums >= 1')
            ->orderBy($order)
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    /**
     * 获取主库关联游戏数量
     *
     * @param string $where
     * @return int|string
     */
    public function gameMasterGameCount(string $where = '')
    {
        return $this->db->table('game as bg')
            ->join('game_list as bgl', 'bg.id=bgl.gameid')
            ->join('similar_game as sg', 'bg.id = sg.fid')
            ->where("bg.name = bgl.name and bg.status = 1 and bg.state = 1 and bg.show_down = 1 and sg.classify = 1 ")
            ->where($where?:[])
            ->groupBy('bg.id')
            ->select('COUNT(*) AS num')
            ->countAllResults();
    }

    /**
     * 获取主库关联游戏
     *
     * @param string $where
     * @param string $field
     * @param integer $limit
     * @param integer $offset
     * @param string $order
     * @param string $group
     * @return array
     */
    public function getMasterOneGameInfo(string $where = "", $field = ['*'])
    {
        return $this->db->table('game as bg')
            ->join('game_list as bgl', 'bg.id=bgl.gameid')
            ->where("bg.name = bgl.name and bg.status = 1 and bg.state = 1 and bg.show_down = 1")
            ->where($where?:[])
            ->select($field)
            ->get()
            ->getRowArray();
    }
    /**
     * @function getListByTag
     */
    public function getListByTag($where = [], $field = '*', $limit = 0, $offset = 0, $order = '')
    {
        $query = $this->db->table('game_tag as t')
            ->join('game_list as bgl','t.gameid=bgl.id','left')
            ->join('game_pack as bgp', 'bgl.id=bgp.gameid', 'left')
            ->where($where);

        return $query->select($field)
            ->limit($limit, $offset)
            ->orderBy($order)
            ->get()
            ->getResultArray();
    }

    /**
     * @function getCountByTag: 获取列表的总数
     */
    public function getCountByTag($where = [])
    {
       return  $this->db->table('game_tag as t')
            ->join('game_list as bgl','t.gameid=bgl.id','left')
            ->where($where)
            ->countAllResults();
//         prx($this->db->getLastQuery());
    }

}