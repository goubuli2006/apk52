<?php

namespace Config;

use App\Helpers\CacheFacade;
use App\Helpers\CacheHelper;
use CodeIgniter\Config\BaseConfig;

class Custom extends BaseConfig
{

    public static $cacheVal = "";

    /**
     * @function getTagState:专题类型
     */
    public static function getTagState()
    {
        $tag_state = array(
            1 => '精选主题',
            2 => '热门主题',
        );
        return $tag_state;
    }

    /**
     * @function getTagState: 排行榜推荐状态
     */
    public static function getTopRecommend()
    {
        $topRecommend = array(
            1 => '普通',
            2 => '热门',
            3 => '经典',
            4 => '最新',
        );

        return $topRecommend;
    }


    /**
     * @function getPlatRecommend: 研发商状态
     */
    public static function getPlatRecommend()
    {
        return array(
            1 => '普通',
            2 => '热门',
            3 => '推荐',
            4 => '停止',
        );
    }

    /**
     * @function getPlatRecommend: 攻略教程上方Tab
     */
    public static function getNewsTabName()
    {
        return [
            0 => '全部',
            1 => '图文攻略',
            2 => '游戏资讯',
            3 => '软件教程',
        ];
    }

    /**
     * @function getImagePath: 获取图片域名
     */
    public static function getImagePath()
    {
        return env('app.volc.uploadDomain');
    }


    /**
     * @function getArticleType: 文章类型
     */
    public static function getArticleType()
    {
        return array(
            1 => '攻略',
            2 => '新闻',
            3 => '软件教程',
        );
    }

    /**
     * @function getPackUnit: 获取下载包配置
     */
    public static function getPackUnit()
    {
        return array(
            1 => 'M',
            2 => 'G',
            3 => 'K',
        );
    }


    /**
     * @function getCategory:
     * @param $classify 1游戏 2应用
     * @param $id 分类id
     */
    public static function getCategory($classify)
    {
        $categoryData = CacheFacade::get(CacheHelper::CATEGORY);

        if ($classify == 2) {
            // 如果是应用的话 对应的数组索引0
            $classify = 1;
        } else {
            // 如果是应用的话 对应的数组索引1
            $classify = 0;
        }
        return $categoryData[$classify];
    }

    public static function getPloyCatData($id)
    {
        $result = [];
        
        if (self::$cacheVal == null) {
            $categoryData = CacheFacade::get(CacheHelper::POLYCAT_CATEGORY);
            self::$cacheVal = $categoryData;
        } else {
            $categoryData = self::$cacheVal;
        }

        if (!empty($categoryData)) {
            foreach ($categoryData as $key => $val) {
                if ($val['id'] == $id) {
                    $result = $val;
                    break;
                }
                if (isset($val['children']) && !empty($val['children'])){
                    foreach ($val['children'] as $k=>$v){
                        if ($v['id'] == $id) {
                            $result = $v;
                            break;
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @function getGameAppType: 游戏、软件分类
     */
    public static function getGameAppType()
    {
        return array(
            1 => '最新',
            2 => '热门',
            3 => '推荐',
        );
    }

    public static function getRedisConfig()
    {
        $redisConfig['host'] = env('redis.hostname');
        $redisConfig['password'] = env('redis.password');
        $redisConfig['port'] = env('redis.port');
        $redisConfig['timeout'] = env('redis.timeout');
        $redisConfig['db_number'] = env('redis.db_number');

        return $redisConfig;
    }


    public function getTypeDesc(){
        //游戏类型说明
        $gameDesc = array(
            0  => '挑战人类极限',
            1  => '来拯救世界吧少年',
            2  => '热血满满 磨拳擦脚！',
            3  => '地铁与公交不二选择',
            4  => '上手就能射击打仗的感觉',
            5  => '化身军事雄才伟略',
            6  => '靠实力也靠运气',
            7  => '沉迷旋律，指尖飞舞',
            8  => '5个亿怎么花？',
            9  => '挑战人类极限',
            10 => '别让他掀桌子',
            11 => '攻略baba救我！',
        );


        $appDesc = array(
           0  => '无纸化高效沟通',
            1  => '你有没有爱上我',
            2  => '资源神器不容错过',
            3  => '瘦身让手机更快',
            4  => '动态壁纸免费用',
            5  => '吃瓜群众首选',
            6  => '手机也能拍大片',
            7  => '折扣好物不用拼',
            8  => '理财就是理生活',
            9  => '解锁城市新地图',
            10 => '口袋里的百宝箱',
            11 => '无纸化高效沟通',
            12 => '稳定不掉线',
        );

        return [
            'game'=>$gameDesc,
            'app'=>$appDesc,
        ];
    }

}