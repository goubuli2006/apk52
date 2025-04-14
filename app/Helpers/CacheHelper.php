<?php

namespace App\Helpers;

use Websitelibrary\CacheApi\CacheManagerService;

class CacheHelper extends CacheManagerService
{
    const POLYCAT_CATEGORY = "polycat:category";
    const CATEGORY = "category";
    const TAGS_GAME_DETAIL = "tags:game:detail";
    const TAGS_APP_DETAIL = "tags:app:detail";

    const M_TOPIC_AUTHOR_DETAIL = 'zt:author:detail'; // 小编推荐
    const M_TOPIC_EDITOR_DETAIL = 'zt:editor:detail'; // 编辑推荐

    const PC_GAME_TOPIC_DETAIL = "zt:game:detail";
    const PC_APP_TOPIC_DETAIL = "zt:app:detail";
    const M_GAME_TOPIC_DETAIL = "zt:m:game:detail";
    const M_APP_TOPIC_DETAIL = "zt:m:app:detail";

    const GAME_VIEW_DETAIL = "game:view:detail";
    const APP_VIEW_DETAIL = "app:view:detail";
    const NEWS_VIEW_DETAIL = "news:view:detail";
    const NEWS_DIGG_LIST = "news:digg:view:detail";

    const ANTITHIEF_WHITE_DOMAIN_KEY = "antithief_white_domain_key";
    const ANTITHIEF_WHITE_DOMAIN_HISTORY_KEY = "antithief_white_domain_history_key";
    const ANTITHIEF_WHITE_DOMAIN_LATEST_KEY = "antithief_white_domain_latest_key";

    const PC_HOME_GAME_TYPE_LIST = "home:game:type:list";
    const PC_HOME_APP_TYPE_LIST = "home:app:type:list";

    public $register = [
        self::POLYCAT_CATEGORY => [
            'className' => \App\Services\PloyCategoryService::class,
            'methodName' => 'cacheCategories',
            'ttl' => 86400,
            'doc' => '站点频道数据'
        ],
        self::CATEGORY => [
            'className' => \App\Services\CategoryService::class,
            'methodName' => 'getCacheCateList',
            'ttl' => 86400,
            'doc' => '站点分类数据'
        ],
        self::TAGS_GAME_DETAIL => [
            'className' => \App\Services\TagService::class,
            'methodName' => 'getTagGameDetailCache',
            'ttl' => 86400,
            'doc' => '游戏详情页猜你喜欢，相关专题数据'
        ],
        self::TAGS_APP_DETAIL => [
            'className' => \App\Services\TagService::class,
            'methodName' => 'getTagAppDetailCache',
            'ttl' => 86400,
            'doc' => '应用详情页猜你喜欢，相关专题数据'
        ],
        self::M_TOPIC_AUTHOR_DETAIL => [
            'className' => \App\Controllers\Mobile\Zt::class,
            'methodName' => 'getTopicAuthorDetailCache',
            'ttl' => 3600,
            'doc' => 'M专题详情页小编推荐'
        ],
        self::M_TOPIC_EDITOR_DETAIL => [
            'className' => \App\Controllers\Mobile\Zt::class,
            'methodName' => 'getTopicEditorDetailCache',
            'ttl' => 3600,
            'doc' => 'M专题详情页编辑推荐'
        ],
        self::PC_GAME_TOPIC_DETAIL => [
            'className' => \App\Services\TagService::class,
            'methodName' => 'getTagGameOrAppListInfo',
            'ttl' => 3600,
            'doc' => 'PC游戏专题分类下游戏列表缓存',
        ],
        self::PC_APP_TOPIC_DETAIL => [
            'className' => \App\Services\TagService::class,
            'methodName' => 'getTagGameOrAppListInfo',
            'ttl' => 3600,
            'doc' => 'PC应用专题分类下应用列表缓存',
        ],
        self::M_GAME_TOPIC_DETAIL => [
            'className' => \App\Services\TagService::class,
            'methodName' => 'getTagGameOrAppListInfo',
            'ttl' => 3600,
            'doc' => 'M游戏专题分类下游戏列表缓存',
        ],
        self::M_APP_TOPIC_DETAIL => [
            'className' => \App\Services\TagService::class,
            'methodName' => 'getTagGameOrAppListInfo',
            'ttl' => 3600,
            'doc' => 'M应用专题分类下应用列表缓存',
        ],
        self::GAME_VIEW_DETAIL => [
            'className' => \App\Services\GameService::class,
            'methodName' => 'setGameDetailView',
            'ttl' => 864000,
            'doc' => '游戏详情页浏览量',
        ],
        self::APP_VIEW_DETAIL => [
            'className' => \App\Services\AppService::class,
            'methodName' => 'setAppDetailView',
            'ttl' => 864000,
            'doc' => '应用详情页浏览量',
        ],
        self::NEWS_VIEW_DETAIL => [
            'className' => \App\Services\NewsService::class,
            'methodName' => 'setNewsDetailView',
            'ttl' => 864000,
            'doc' => '资讯详情页浏览量',
        ],
        self::NEWS_DIGG_LIST => [
            'className' => \App\Services\NewsService::class,
            'methodName' => 'setNewsDetailView',
            'ttl' => 86400,
            'doc' => '资讯详情页点赞',
        ],
        self::ANTITHIEF_WHITE_DOMAIN_KEY => [
            'ttl' => 600,
            'doc' => '防盗链域名白名单',
        ],
        self::ANTITHIEF_WHITE_DOMAIN_HISTORY_KEY => [
            'ttl' => 86400*30,
            'doc' => '历史白名单记录',
        ],
        self::ANTITHIEF_WHITE_DOMAIN_LATEST_KEY => [
            'ttl' => -1,
            'doc' => '最新白名单记录永久',
        ],
        self::PC_HOME_GAME_TYPE_LIST => [
            'className' => \App\Controllers\Pc\Home::class,
            'methodName' => 'getHomeGameList',
            'ttl' => 7200,
            'doc' => '首页游戏分类列表',
        ],
        self::PC_HOME_APP_TYPE_LIST => [
            'className' => \App\Controllers\Pc\Home::class,
            'methodName' => 'getHomeAppList',
            'ttl' => 7200,
            'doc' => '首页软件分类列表',
        ],
    ];
}