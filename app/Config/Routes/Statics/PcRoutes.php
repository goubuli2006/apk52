<?php

$routes->get('/', 'Pc\Home::index');

//手机游戏
$routes->get('game/', 'Pc\Game::index');
$routes->get('game/(\d+)/', 'Pc\Game::index/$1');
$routes->get('game/game_([a-zA-Z]+)/', 'Pc\Game::index/$1');
$routes->get('game/game_([a-zA-Z]+)/(\d+)/', 'Pc\Game::index/$1/$2');

//手机软件
$routes->get('app/', 'Pc\Game::soft');
$routes->get('app/(\d+)/', 'Pc\Game::soft/$1');
$routes->get('app/app_([a-zA-Z]+)/', 'Pc\Game::soft/$1');
$routes->get('app/app_([a-zA-Z]+)/(\d+)/', 'Pc\Game::soft/$1/$2');


//专题汇总
$routes->get('subclass', 'Pc\Topic::index');
$routes->get('subclass/(\d+)/', 'Pc\Topic::index/$1');


//游戏/应用 详情页
$routes->get('game/([a-zA-Z0-9_]+)/', 'Pc\Game::gameDetail/$1');
$routes->get('app/([a-zA-Z0-9_]+)/', 'Pc\Game::softDetail/$1');

//应用下载页
$routes->get('game/([a-zA-Z0-9_]+)/download', 'Pc\Game::gameDownload/$1');
$routes->get('app/([a-zA-Z0-9_]+)/download', 'Pc\Game::softDownload/$1');



//游戏 软件合集
$routes->get('subclass/game/', 'Pc\Topic::index');
$routes->get('subclass/app/', 'Pc\Topic::index');
$routes->get('subclass/game/(\d+)/', 'Pc\Topic::index/$1');
$routes->get('subclass/app/(\d+)/', 'Pc\Topic::index/$1');

//专题详情
$routes->get('subclass/([a-zA-Z0-9]+)/', 'Pc\Topic::detail/$1');
$routes->get('subclass/([a-zA-Z0-9]+)/(\d+)/', 'Pc\Topic::detail/$1/$2/');

