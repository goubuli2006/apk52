<?php

namespace App\Controllers\Mobile;

use App\Models\AppListModel;
use App\Models\GameListModel;
use App\Models\NewsModel;
use App\Models\TagModel;
use CodeIgniter\Controller;
use Config\Custom;

class Crontab extends Controller
{
    /**
     *  周浏览清0
     *  更新游戏 上周 和 昨天的浏览量 更新为0
     */
    public function weekViewSetZero()
    {
        echo '开始清0……';
        $gameList = new GameListModel();
        $gameList->updateBatchData(['wview' => 0], " wview>0");

        $appList = new AppListModel();
        $appList->updateBatchData(['wview' => 0], " wview>0");

        $newList = new NewsModel();
        $newList->updateBatchData(['wview' => 0], " wview>0");
        echo '结束清0……';
    }

    /**
     *  月浏览量 每月1号清0  mview
     */
    public function monthViewSetZero()
    {
        echo '开始清0……';
        $gameList = new GameListModel();
        $gameList->updateBatchData(['mview' => 0], " mview>0");
        $appList = new AppListModel();
        $appList->updateBatchData(['mview' => 0], " mview>0");
        $newList = new NewsModel();
        $newList->updateBatchData(['mview' => 0], " mview>0");
        echo '结束清0……';
    }

    /**
     * @function statisticsNewsView: 统计资讯浏览量
     */
    public function statisticsNewsView()
    {
        echo '开始统计……';
        $arr = array();
        $time = date('YmdH0000', strtotime('-1 hours'));
        //拼接redis key
        $keyData = array('0' => 'mview', '1' => 'wview', '2' => 'tview');

        foreach ($keyData as $key => $val) {
            $redisKey = 'bdgame:gl:' . $time . ':' . $val;

            $this->insertStatisticsView($redisKey, $val, 1);
        }
    }

    /**
     * @function statisticsNewsView: 统计game浏览量
     */
    public function statisticsGameView()
    {
        echo '开始统计……';
        $arr = array();
        $time = date('YmdH0000', strtotime('-1 hours'));
        //拼接redis key
        $keyData = array('0' => 'mview', '1' => 'wview');

        foreach ($keyData as $key => $val) {
            $redisKey = 'bdgame:gameView:' . $time . ':' . $val;

            $this->insertStatisticsView($redisKey, $val, 2);
        }
    }

    /**
     * @function statisticsNewsView: 统计应用浏览量
     */
    public function statisticsAppView()
    {
        echo '开始统计……';
        $arr = array();
        $time = date('YmdH0000', strtotime('-1 hours'));
        //拼接redis key
        $keyData = array('0' => 'mview', '1' => 'wview');

        foreach ($keyData as $key => $val) {
            $redisKey = 'bdgame:appView:' . $time . ':' . $val;

            $this->insertStatisticsView($redisKey, $val, 3);
        }
    }

    /**
     * @function insertStatisticsView: 修改统计数量
     */
    public function insertStatisticsView($redisKey, $field, $type = 1)
    {
        $redisConf = Custom::getRedisConfig();

        $redis = new \Redis();
        $redis->connect($redisConf['host'], $redisConf['port']);
        $redis->select(env('redis.db_number'));

        $stime = explode(' ', microtime());
        $stimes = $stime[1] + $stime[0];

        $list = $redis->hGetAll($redisKey);
        $arr = array();
        if ($list) {
            //统计相同id的和
            foreach ($list as $k => $val) {

                if (!isset($arr[$k])) {
                    $arr[$k] = $val;
                } else {
                    $arr[$k] += $val;
                }
            }
        }

        if (empty($arr)){
            echo '数据为空,结束。';
            die;
        }
        if ($arr) {
            $arrs = [];

            foreach ($arr as $k => $val) {
                if ($k) {
                    $data['id'] = $k;
                    $data[$field] = $val;
                    $arrs[] = $data;
                }
            }

            $res = '';
            if ($type == 1) {
                $newList = new NewsModel();
                $res = $newList->setNewsView($arrs, $field); //批量资讯浏览量更新
            } else if ($type == 2) {
                $gameList = new GameListModel();
                $res = $gameList->setGameView($arrs, $field);//批量游戏浏览量更新
            } else if ($type == 3) {
                $appList = new AppListModel();
                $res = $appList->setAppView($arrs, $field); //批量游戏下载量更新
            }

            $etime = explode(' ', microtime());
            $etimes = $etime[1] + $etime[0];
            $times = $etimes - $stimes;
            echo "共更新记录 ".count($arrs)." 条，耗时 $times s";
        }

    }

    /**
     * 统计应用专题数据
     */
    public function updateAppTagsGameNums()
    {
        $input = $this->request->getGet();
        set_time_limit(0);
        echo 'start-';
        if (isset($input['data']) && !empty($input['data'])) {
            $starttime = strtotime($input['data']);
            $endtime = $starttime + 86400;
        } else {
            $starttime = strtotime(date('Y-m-d', time()));
            $endtime = $starttime + 86400;
        }
        //获取今日更新或新增游戏tags
        $gamesWhere = "classify = 2 and status = 1  and (uptime  between {$starttime} and {$endtime} )";

        $appList = new AppListModel();
        $gamesData = $appList->getTableList($gamesWhere, 'tags');

        $list = array();
        if (count($gamesData) > 0) {
            foreach ($gamesData as $value) {
                $value['tags'] = trim($value['tags'], ',');
                if ($value['tags']) {
                    $value['tags'] = explode(',', $value['tags']);
                    if (count($value['tags']) > 0) {
                        foreach ($value['tags'] as $v) {
                            $list[$v] = $v;
                        }
                    }
                }
            }
        }
        $updateData = array();
        if (!empty($list)) {
            foreach ($list as $key => $val) {
                $gwhere = " classify = 2 and status = 1 and state = 1 and find_in_set({$val},tags)";
                $total = $appList->getTableListCount($gwhere);
                if ($total > 0) {
                    $updateData[$key]['game_num'] = $total;
                    $updateData[$key]['game_num_time'] = time();
                    $updateData[$key]['id'] = $val;
                }
            }
            $tagModel = new TagModel();

            if ($updateData){
                $flag =$tagModel->upTagsBatch($updateData);
                if ($flag > 0) {
                    echo '-' . date('Y-m-d H:i:s', time()) . '-更新完成';
                    die;
                } else {
                    echo '-' . date('Y-m-d H:i:s', time()) . '-更新失败';
                    die;
                }
            }else{
                echo '-' . date('Y-m-d H:i:s', time()) . '-数据为空更新失败';
                die;
            }
        } else {
            echo '-' . date('Y-m-d H:i:s', time()) . '-数据为空更新失败';
            die;
        }
    }

    /**
     * 统计游戏专题数据
     */
    public function updateGameTagsGameNums()
    {
        $input = $this->request->getGet();
        set_time_limit(0);
        echo 'start-';
        if (isset($input['data']) && !empty($input['data'])) {
            $starttime = strtotime($input['data']);
            $endtime = $starttime + 86400;
        } else {
            $starttime = strtotime(date('Y-m-d', time()));
            $endtime = $starttime + 86400;
        }
        //获取今日更新或新增游戏tags
        $gamesWhere = "classify = 1 and status = 1  and (uptime  between {$starttime} and {$endtime} )";

        $gameList = new GameListModel();
        $gamesData = $gameList->getTableList($gamesWhere, 'tags');

        $list = array();
        if (count($gamesData) > 0) {
            foreach ($gamesData as $value) {
                $value['tags'] = trim($value['tags'], ',');
                if ($value['tags']) {
                    $value['tags'] = explode(',', $value['tags']);
                    if (count($value['tags']) > 0) {
                        foreach ($value['tags'] as $v) {
                            $list[$v] = $v;
                        }
                    }
                }
            }
        }
        $updateData = array();
        if (!empty($list)) {
            foreach ($list as $key => $val) {
                $gwhere = " classify = 1 and status = 1 and state = 1 and find_in_set({$val},tags)";
                $total = $gameList->getTableListCount($gwhere);
                if ($total > 0) {
                    $updateData[$key]['game_num'] = $total;
                    $updateData[$key]['game_num_time'] = time();
                    $updateData[$key]['id'] = $val;
                }
            }
            $tagModel = new TagModel();

            if ($updateData){
                $flag =$tagModel->upTagsBatch($updateData);
                if ($flag > 0) {
                    echo '-' . date('Y-m-d H:i:s', time()) . '-更新完成';
                    die;
                } else {
                    echo '-' . date('Y-m-d H:i:s', time()) . '-更新失败';
                    die;
                }
            }else{
                echo '-' . date('Y-m-d H:i:s', time()) . '-数据为空更新失败';
                die;
            }
        } else {
            echo '-' . date('Y-m-d H:i:s', time()) . '-数据为空更新失败';
            die;
        }
    }

    /**
     * @function releaseNews: 新闻定时发布
     */
    public function releaseNews(){
        echo '开始运行……';
        $stime  = explode(' ',microtime());
        $stimes = $stime[1] + $stime[0];

        $time   = time();
        $res  = 0;
        $where  = "status = 2 and release_time > 0 and release_time <= $time";
        $newList = new NewsModel();
        $list = $newList->getTableList($where, "id,release_time AS addtime,IF(status = 2,1,2) AS status", 1000, 0,'id desc');
        if($list){
            $res = $newList->upNewsBatch($list); //批量更新
        }
        $etime  = explode(' ',microtime());
        $etimes = $etime[1] + $etime[0];
        $times  = $etimes - $stimes;

        echo "共发布新闻 {$res} 条，耗时 $times s";
    }
}