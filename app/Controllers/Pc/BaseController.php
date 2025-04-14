<?php

namespace App\Controllers\Pc;

use App\Enum\CacheEnum;
use App\Enum\PcEnum;
use App\Helpers\CacheFacade;
use App\Helpers\CacheHelper;
use App\Services\AdService;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Services\PloyCategoryService;
use App\Services\CategoryService;
use App\Services\CommonService;
use App\Services\GameService;
use Websitelibrary\CacheApi\CacheManagerService;

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
        $_ENV['app.m.domainUrl'] = env('app.domainUrl');
        $_ENV['app.domainUrl']   = env('app.pc.domainUrl');

        CacheFacade::get(CacheHelper::POLYCAT_CATEGORY);
        CacheFacade::get(CacheHelper::CATEGORY);
        CommonService::init();
        $commonInfo = CommonService::pcCommonData();
        CommonService::commonOtherDataToTemplate($commonInfo);

    }
    
    public function show_404()
    {
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");
        // 将会输出一个自定义的视图
        $info['nav'] = 0;

        echo view('/Statics/Pc/errors/404.html', $info);
        exit;
    }
    
}
