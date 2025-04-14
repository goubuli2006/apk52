<?php

$routes->get('/', 'Mobile\Home::index');

//手机游戏
$routes->get('game/', 'Mobile\Game::index');
$routes->get('game/(\d+)/', 'Mobile\Game::index/$1');
$routes->get('game/game_([a-zA-Z]+)/', 'Mobile\Game::index/$1');
$routes->get('game/game_([a-zA-Z]+)/(\d+)/', 'Mobile\Game::index/$1/$2');

//手机软件
$routes->get('app/', 'Mobile\Game::soft');
$routes->get('app/(\d+)/', 'Mobile\Game::soft/$1');
$routes->get('app/app_([a-zA-Z]+)/', 'Mobile\Game::soft/$1');
$routes->get('app/app_([a-zA-Z]+)/(\d+)/', 'Mobile\Game::soft/$1/$2');

//专题汇总
$routes->get('subclass', 'Mobile\Topic::index');
$routes->get('subclass/(\d+)/', 'Mobile\Topic::index/$1');

//游戏/应用 详情页
$routes->get('game/([a-zA-Z0-9_]+)/', 'Mobile\Game::gameDetail/$1');
$routes->get('app/([a-zA-Z0-9_]+)/', 'Mobile\Game::softDetail/$1');

//应用下载页
$routes->get('game/([a-zA-Z0-9_]+)/download', 'Mobile\Game::gameDownload/$1');
$routes->get('app/([a-zA-Z0-9_]+)/download', 'Mobile\Game::softDownload/$1');



//游戏 软件合集
$routes->get('subclass/game/', 'Mobile\Topic::index');
$routes->get('subclass/app/', 'Mobile\Topic::index');
$routes->get('subclass/game/(\d+)/', 'Mobile\Topic::index/$1');
$routes->get('subclass/app/(\d+)/', 'Mobile\Topic::index/$1');

//专题详情
$routes->get('subclass/([a-zA-Z0-9]+)/', 'Mobile\Topic::detail/$1');
$routes->get('subclass/([a-zA-Z0-9]+)/(\d+)/', 'Mobile\Topic::detail/$1/$2/');

//404生成文件
$routes->post('ajax/404', 'Mobile\Ajax::createNotFound');

// 路由找不到 跳转到 404
$routes->set404Override('App\Controllers\Mobile\Errors::show404');

// 清除redis操作
$routes->post('get/pregRedisList', 'Mobile\CacheRedisFile::pregRedisList');
$routes->post('get/getKeyData', 'Mobile\CacheRedisFile::getRedisData');
$routes->post('del/pregRedisDelKey', 'Mobile\CacheRedisFile::pregRedisDelKey');


