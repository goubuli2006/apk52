<?php

/**
 * Api 路由
 */
$routes->group('api', function ($routes) {
    //缓存文件获取
    $routes->post('getDirFile', 'Api\CacheFiledel::getDirFile');
    //缓存文件清除
    $routes->post('delDirOrFile', 'Api\CacheFiledel::delDirOrFile');

    // 缓存暴露出来的接口
    $routes->post('cache/search','Api\CacheApi::pregRedisList');
    // 根据key获取值
    $routes->post('cache/get','Api\CacheApi::getDataByKey');
    //  根据 key 删除 redis 数据
    $routes->post('cache/del','Api\CacheApi::delDataByKey');
    //  获取所有的key列表
    $routes->post('cache/list','Api\CacheApi::getKeyList');
    $routes->get('health','Api\CacheApi::health');
    //cache api
    $routes->get('sync/list','Api\CacheApi::syncCacheList');
});


//404生成文件
$routes->post('ajax/404', 'Api\Ajax::createNotFound');
//上报
$routes->get('home', 'Api\Report::index');

$routes->post('api/permission','Common\Downs::getPermission');



