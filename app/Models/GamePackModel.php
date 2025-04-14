<?php

namespace App\Models;

class GamePackModel extends BaseModel
{

    protected $table = 'game_pack';

    protected $allowedFields = [
        'id', 'gameid', 'and_ver', 'and_size', 'and_url',
        'and_unit', 'ios_ver', 'ios_size', 'ios_url', 'ios_unit',
        'addtime', 'uptime', 'checktime', 'and_lock', 'ios_lock', 'flag', 'pc_url'
    ];


    /**
     * @function getGamePackInfoList: 获取所有软件包记录
     */
    public function getGamePackInfoList($gameLastId, $limit)
    {
        return $this->db->table('game_pack as gp')
            ->join('game_list as gl', 'gp.gameid = gl.id')
            ->select(['gp.id', 'gp.gameid', 'gp.and_url', 'gl.name'])
            ->where('gp.and_url !=', '')
            ->where('gp.id >', $gameLastId)
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    public function getGamePackCount()
    {
        return $this->db->table('game_pack')
            ->where('and_url !=', '')
            ->countAllResults();
    }

    public function getPackRow($gameId)
    {

    }

}



