<?php
//脚本路由
// 更新游戏 上周 和 昨天的浏览量 更新为0
$routes->cli('crontab/weekViewSetZero', 'Common\Crontab::weekViewSetZero');
// 月浏览量 每月1号清0  mview
$routes->cli('crontab/monthViewSetZero', 'Common\Crontab::monthViewSetZero');

//统计 资讯 游戏 应用 浏览量
$routes->cli('crontab/statisNewsView', 'Common\Crontab::statisticsNewsView');
$routes->cli('crontab/statisGameView', 'Common\Crontab::statisticsGameView');
$routes->cli('crontab/statisAppView', 'Common\Crontab::statisticsAppView');

//统计应用和游戏专题总数
$routes->cli('crontab/updateAppTagsGameNums', 'Common\Crontab::updateAppTagsGameNums');
$routes->cli('crontab/updateGameTagsGameNums', 'Common\Crontab::updateGameTagsGameNums');

//资讯脚本定时发布
$routes->cli('crontab/releaseNews', 'Common\Crontab::releaseNews');
//统计应用和游戏专题总数(专题数量为0)
$routes->cli('crontab/updateAllZeroAppTagsGameNums', 'Common\Crontab::updateAllZeroAppTagsGameNums');