<?php namespace App\Services;

use App\Models\AppListModel;

/**
 * 应用
 */
class AppService extends BaseService
{
    public $appList;

    public function __construct()
    {
        $this->appList = new AppListModel();
    }

    public function getAppList($field, $limit, $order = "uptime desc", $where = "", $offset = 0)
    {
        $data = $this->appList->getNewAppList("status=1 and state=1 ".$where, [], $field, $limit, $offset, $order);

        return $this->getFormatData($data);
    }

    public function getFormatList($field, $limit, $order = "uptime desc", $where = "", $offset = 0)
    {
        return $this->getAppList($field, $limit, $order, $where, $offset);
    }

    public function getListByTag($field, $limit, $order = "uptime desc", $where = "", $offset = 0){
        $data = $this->appList->getListByTag("status=1 and state=1 " . $where,  $field, $limit, $offset, $order);
        return $this->getFormatData($data);
    }

    public function getAppTypeCount($gameType)
    {
        $gameCount = [];

        if ($gameType && !empty($gameType['children'])) {
            foreach ($gameType['children'] as $key => $val) {
                $where_count = 'classify = 2 and status = 1 and state=1 and type= ' . $val['id'];
                $total = $this->appList->getTableListCount($where_count, []);
                $gameCount[$val['id']] = $total;
            }
        }

        return $gameCount;
    }

    /**
     * 获取主库游戏项目 列表为一对一
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
        $data = $this->appList->getMasterGameList($where, $field, $limit, $offset, $order);
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
        return $this->appList->gameMasterGameCount($where);
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
        $data = $this->appList->getMasterOneGameInfo($where, $field);
        return $data;
    }

    public function setAppDetailView()
    {
        return 1;
    }
}