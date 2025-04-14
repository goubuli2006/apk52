<?php namespace App\Services;

use App\Enum\CategoryEnum;
use App\Models\GameListModel;
use Config\Custom;

/**
 * 游戏
 */
class GameService extends BaseService
{
    public $gameList;

    public function __construct()
    {
        $this->gameList = new GameListModel();
    }

    public function getGameList($field, $limit, $order = "uptime desc", $where = "", $offset = 0)
    {
        $data = $this->gameList->getNewGameList("status=1 and state=1 " . $where, [], $field, $limit, $offset, $order);
        return $this->getFormatData($data);
    }

    public function getFormatList($field, $limit, $order = "uptime desc", $where = "", $offset = 0)
    {
        return $this->getGameList($field, $limit, $order, $where, $offset);
    }

    public function getListByTag($field, $limit, $order = "uptime desc", $where = "", $offset = 0){
        $data = $this->gameList->getListByTag("status=1 and state=1 " . $where,  $field, $limit, $offset, $order);
        return $this->getFormatData($data);
    }

    public function getGameTypeCount($gameType)
    {
        $gameCount = [];

        if ($gameType && !empty($gameType['children'])) {
            foreach ($gameType['children'] as $key => $val) {
                $where_count = 'classify = 1 and status = 1 and state=1 and type= ' . $val['id'];
                $total = $this->gameList->getTableListCount($where_count, []);
                $gameCount[$val['id']] = $total;
            }
        }

        return $gameCount;
    }

    /**
     * 获取主库游戏项目 列表一对一
     *
     * @param [string] $field
     * @param [int] $limit
     * @param string $order
     * @param string $where
     * @param integer $offset
     * @return array
     */
    public function getMasterGameList($field, $limit, $order = "bgl.uptime desc", $where = "", $offset = 0)
    {
        $data = $this->gameList->getMasterGameList($where, $field, $limit, $offset, $order);
        return $this->getFormatData($data);
    }

    /**
     * 获取主库关联游戏数量
     *
     * @param string $where
     * @return int|string
     */
    public function gameMasterGameCount(string $where = "")
    {
        return $this->gameList->gameMasterGameCount($where);
    }

    /**
     * 获取主库游戏详情
     *
     * @param [string] $field
     * @param [int] $limit
     * @return array
     */
    public function getMasterOneGameInfo($field, string $where = "")
    {
        $data = $this->gameList->getMasterOneGameInfo($where, $field);
        return $data;
    }

    public function setGameDetailView()
    {
        return 1;
    }
}