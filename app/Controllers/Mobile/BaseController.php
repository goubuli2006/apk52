<?php

namespace App\Controllers\Mobile;

use App\Helpers\CacheHelper;
use App\Helpers\RedisHelper;
use App\Helpers\CacheFacade;
use App\Services\CommonService;
use App\Services\GameService;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Custom;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    protected $isios;
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        CacheFacade::get(CacheHelper::POLYCAT_CATEGORY);
        CacheFacade::get(CacheHelper::CATEGORY);

        CommonService::init();
        $commonInfo = CommonService::mobileCommonData();
        CommonService::commonOtherDataToTemplate($commonInfo);

        $this->isios = $this->get_device_type();
    }

    /**
     * @function get_device_type: 获取系统类型
     */
    public function get_device_type()
    {
        //全部变成小写字母
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);

        $type = 0;
        //分别进行判断
        if (strpos($agent, 'iphone') || strpos($agent, 'ipad')) {
            $type = 1;
        } else if (strpos($agent, 'android') !== false) {
            $type = 2;
        }
        return $type;
    }

    public function getRequestUri()
    {
        $url = $_SERVER['REQUEST_URI'];

        if (strrpos($url, '-') !== false) {
            $urls = pathinfo($url);
            $gameParam = $urls['filename'];
            return $gameParam;
        }
        return;
    }

    public function show_404()
    {
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");

        $gameService = new GameService();
        $gfields = "bgl.id,bgl.name,bgl.icon,bgl.type,bgl.classify,bgl.shortname,bgl.game_score,bgl.uptime,bgl.gameid,bgp.and_url as downurl,bgl.tags,bgl.union_id";
        $gameList = $gameService->getGameList($gfields, 8, 'uptime desc');
        $info['gameList']   = $gameList;
        $info['currentUrl'] = rtrim(env('app.domainUrl'),'/').$_SERVER['REQUEST_URI'];
        $info['domain'] = env('app.domainUrl');
        $info['nav'] = 0;

        echo view('/Statics/M/errors/404.html',$info);
        exit;
    }

    public function getKeyToWriteGameViewRedis($redisPrefix, $id, $type)
    {
        $data = array('0' => 'mview', '1' => 'wview','2'=>'twview');
        $this->getKeyWriteToRedis($redisPrefix, $id, $type, $data);
    }

    public function getKeyWriteToRedis($redisPrefix, $id, $type, $data)
    {
        $time = date('YmdH0000');
        $currentTime = time();
        foreach ($data as $key => $val) {
            $redisKey = $redisPrefix . ":" . $type . ':' . $time . ':' . $val;

            $this->writeRedis($redisKey, $id, $type, $data);
        }
    }

    public function writeRedis($redisKey, $id, $type, $data)
    {
        $redisHash = $this->getRedisConnect();
        $online = $redisHash->setHashKey($redisKey);
        if (!$online->get((string)$id)) {
            $online->set((string)$id, '1');
        } else {
            $count = $online->get((string)$id);
            $count = $count + 1;
            $online->set((string)$id, (string)$count);
        }

        // 设置过期时间
        $online->setExpire(86400 * 10);
    }

    public function getRedisConnect()
    {
        $redisHash = new RedisHelper(\Config\Custom::getRedisConfig());
        return $redisHash;
    }

    public function setRedisData($redisKey, $id, $data, $outTime)
    {
        $redisHash = $this->getRedisConnect();


        $online = $redisHash->setHashKey($redisKey);
        if (!$online->get((string)$id)) {
            $online->set((string)$id, json_encode($data));
            // 设置过期时间
            $online->setExpire($outTime);
        } else {
            return $online->get((string)$id);
        }

    }

    public function getRedisData($redisKey, $id)
    {
        $redisHash = $this->getRedisConnect();
        $online = $redisHash->setHashKey($redisKey);
        return $online->get((string)$id);
    }

    public function getTypeInfo($classify, $id)
    {
        $gameType = [];
        if ($classify == 1) {
            $gameType = Custom::getCategory(1);
        } else {
            $gameType = Custom::getCategory(2);
        }

        if ($gameType) {
            foreach ($gameType['children'] as $key => $val) {
                if ($val['id'] == $id) {
                    return $val;
                }
            }
        }

        return [];
    }

    public function getCommonData()
    {
        $gameTypeData = \Config\Custom::getCategory(1);
        $appTypeData = \Config\Custom::getCategory(2);

        $view = \Config\Services::renderer();
        $view->setVar('gameTypeData', $gameTypeData);
        $view->setVar('appTypeData', $appTypeData);
        $view->setVar('domain', env('app.domainUrl'));
        $view->setVar('pc_url', env('app.pc.domainUrl'));

        $view->setVar('domain', env('app.domainUrl'));
        $view->setVar('domainName', env('app.domainName'));

        $view->setVar('image_path', env('app.volc.uploadDomain'));
        $view->setVar('currentUrl',str_replace(env('app.mobile.domainUrl'), env('app.pc.domainUrl'), current_url()));


    }

    public function getPcUrl($url)
    {
        return str_replace(env('app.domainUrl'), env('app.pc.domainUrl'), $url);
    }
}
