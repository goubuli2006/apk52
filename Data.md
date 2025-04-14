### 用于站点参考快速复制


#### 轮播图 一张大图带着一个游戏
```
$lbLists = $adService->getAdvert(PcEnum::PC_AD_HOME_LB_INDEX);
foreach($lbLists as $k => &$val) {
    $gidStr = mb_substr($val['remark'], 0, 1);
    $ginfo = $adService->advOtherGameOrAppPack($val['type'], 0, $gidStr);
    $val['gameInfo'] = isset($ginfo[0]) ? $ginfo[0] : [];
}

//模板
<div class="bRecT">
    <div class="sliders">
        <ul class="cfx">
<?php foreach ($lbLists as $ke => $val) {?>
            <li>
                <a href="<?=$val['url']?>" target="_blank" title="<?=$val['describe']?>">
                    <img src="<?=$val['img']?>" alt="<?=$val['describe']?>" />
                </a>
            </li>
<?php } ?>
        </ul>
        <dl class="dot">

<?php $count = count($lbLists); for($i=0;$i<$count;$i++) { ?>
            <dd class="<?php if($i==0) {echo 'cur';}?>"></dd>
<?php } ?>
        </dl>
                
        <div class="sliderBox">
<?php foreach ($lbLists as $ke => $val) {?>
            <div class="info current">
                <img src="<?=$val['gameInfo']['icon']?>" alt="<?=$val['gameInfo']['name']?>">
                <div><p><?=$val['gameInfo']['name']?></p><span><?=$val['gameInfo']['type_info']?> | <?=$val['gameInfo']['size_format']?:'0M'?></span><span><?=date('Y-m-d', $val['gameInfo']['uptime'])?>更新</span></div>
            </div>
<?php } ?>
        </div>
    </div>
    <i class="ico"></i>
</div>
```

#### 广告位链接形式

```
$topZtLists = $adService->getAdvert(PcEnum::PC_AD_TOP_ZT_INDEX);

//模板
<?php foreach($topZtLists as $key=>$val){?>
<?=$val['img']?>
        <a href="<?=$val['url']?>" target="_blank" title="<?=$val['describe']?>" ><?=$val['describe']?></a> 
<?php } ?>
```

#### 广告位游戏、应用形式

```
$newSoftRecommAllList = $adService->getAdvGameOrAppPackList(PcEnum::PC_AD_HOT_APP_LISTS);

//模板
<?php foreach($newSoftRecommAllList as $val){ ?>
    <li>
        <a href="<?=$val['a_href']?>" target="_blank" class="img"><img data-original="<?=$val['icon']?>" alt="<?=$val['name']?>" title="<?=$val['name']?>"></a>
        <a href="<?=$val['a_href']?>" target="_blank" class="name"><?=$val['name']?></a>
        <a href="<?=$val['a_href']?>" target="_blank" class="btn">查看</a>
    </li>
<?php } ?>

```
#### 软件分类 名称下带着多条数据

```
$gfield = 'bgl.id,bgl.title,bgl.name,bgl.shortname,bgp.and_url as downurl,bgl.icon,bgl.type,bgl.classify,bgl.uptime,bgp.and_size as size,bgp.and_unit as unit';
$appCategoryList = $categoryModel->getTableLists(['pid' => CategoryEnum::SOFT_CATE_PID]);
$appNewMobileTypeLists = [];
$gameListWhere = ' and classify = 2';
foreach ($appCategoryList as $v) {
    $typeWhere = $gameListWhere . ' and type ='. $v['id'];
    $appNewMobileTypeList = $appService->getAppList($gfield, 21, 'bgl.uptime desc', $typeWhere);
    $appNewMobileTypeLists[$v['name']] = $appNewMobileTypeList;
}

//模板
<?php foreach($appNewMobileList as $key => $val){ ?>
        <li <?php if(empty($val['downurl'])){echo 'class="no"';}?> >
            <a href="<?=$val['a_href']?>" target="_blank" title="<?=$val['name']?>">
                <img src="<?=$val['icon']?>" alt="<?=$val['name']?>">
                <p><?=$val['name']?></p>
                <p><?=date('Y-m-d',$val['uptime'])?>更新</p>
                <span><?php if($val['downurl']){echo '免费下载';}else{echo '立即查看';}?></span>
            </a>
        </li>
<?php } ?>

```

#### 游戏主库相关版本

```
$gameAboutVersion = [];
if ($gameInfo['gameid']) {
    $aboutWhere = " and bgl.gameid = " .$gameInfo['gameid'];
    $aboutField = $gfield . ',bgp.and_ver';
    $gameAboutVersion = $gameService->getAppList($aboutField, 10, 'bgl.uptime desc', $aboutWhere);
}

//模板
<?php foreach($gameAboutVersion as $k => $val){ ?>
    <li>
        <a href="<?=$val['a_href']?>" title="<?=$val['name']?>">
            <img src="<?=$val['icon']?>" alt="<?=$val['name']?>" />
            <div>
                <p><?=$val['name']?></p>
                <span><?=$val['and_ver']?></span>
                <span><?=date('Y-m-d', $ginfo['uptime'])?>更新</span>
            </div>
            <span>下载</span>
        </a>
    </li>
<?php } ?>
```

#### game相关文章

```
$newfields = 'id,gameid,title,uptime,list_img,type,describe,rand_key';
$newsList = $newsService->getNewsList($newfields, 6, 'gameid = '. $gameInfo['id'] .' and status = 1');
if (!$newsList) {
    $newsList = $newsService->getNewsList($newfields, 6, 'status = 1 and type = 3');
}

//模板
<?php foreach($newsList as $k => $val){ ?>
        <li>
            <a href="<?=$val['href']?>" target="_blank" title="<?=$val['title']?>"><img src="<?=$val['list_img']?>" alt="<?=$val['title']?>"></a>
            <div class="info">
                <a href="<?=$val['href']?>" target="_blank" title="<?=$val['title']?>"><?=$val['title']?></a>
                <p><?=$val['describe']?></p>
                <span>更新时间：<?=date('Y-m-d H:i', $val['uptime'])?></span>
            </div>
        </li>
<?php } ?>
```

#### game相关游戏

```
//相关游戏 该款游戏分类最新数据
$gameAboutCateList = $gameService->getAppList($gfield, 8, 'bgl.uptime desc', ' and type = '.$gameInfo['type']. ' and bgl.id != '. $gameInfo['id']);

//模板
<?php foreach($gameAboutCateList as $k => $val){ ?>
        <li>
            <a href="<?=$val['a_href']?>" target="_blank" title="<?=$val['name']?>">
                <img src="<?=$val['icon']?>" alt="<?=$val['name']?>">
                <p><?=$val['name']?></p>
                <p><?=date('Y-m-d', $ginfo['uptime'])?>更新</p>
                <span>免费下载</span>
            </a>
        </li>
<?php } ?>

```

#### 新闻浏览量排序

```
//热门资讯 最近一周浏览量
$hotNewsList = $newsService->getNewsList($contentfields, 5, ['status' => 1, 'type' => $newsInfo['type']], 0, 'wview desc,uptime desc');

//模板
<?php foreach($hotNewsList as $key=>$val){?>
        <li>
            <a href="<?=$val['href']?>" target="_blank" title="<?=$val['title']?>">
                <img src="<?=$val['list_img']?>" alt="<?=$val['title']?>" />
                <div>
                    <p><?=$val['title']?></p>
                    <span><?=date("Y-m-d",$val['uptime']) ?></span>
                </div>
            </a>
        </li>
<?php } ?>	

```

#### 新闻相关文章

```
//相关文章
$ntfields = $contentfields.',author,describe';
if ($newsInfo['gameid']) {
    $newsRelationList = $newsService->getNewsList($ntfields, 5, 'gameid = '. $newsInfo['gameid'] .' and status = 1 and id != '. $id);
} else {
    $newsRelationList = $newsService->getNewsList($ntfields, 5, 'status = 1 and type = '. $newsInfo['type'] .' and id != '. $id);
}

//模板
<?php foreach($newsRelationList as $k => $val){?>
        <li>
            <a href="<?=$val['href']?>" target="_blank" title="<?=$val['title']?>"><img src="<?=$val['list_img']?>" alt="<?=$val['title']?>"></a>
            <div class="info">
                <a href="<?=$val['href']?>" target="_blank" title="<?=$val['title']?>"><?=$val['title']?></a>
                <p><?=$val['describe']?></p>
                <div><span>更新时间：<?=date("Y-m-d H:i:s",$val['uptime'])?> </span><span>作者：<?php if($val['author']){ ?><?=$val['author']?><?php }else{ ?>尼克资源网<?php } ?></span><i>2.8万</i></div>
            </div>
        </li>
<?php } ?>	
```

#### 最受期待排行榜

```
//最受期待 应用游戏当月最高 各五个排序吧 月周浏览量
$gameMonthTop5 = $gameService->getGameList($gfield.',bgl.mview', 5, 'bgl.mview desc,bgl.wview desc,bgl.uptime desc', ' and classify = 1');
$appMonthTop5 = $appService->getAppList($gfield.',bgl.mview', 5, 'bgl.mview desc,bgl.wview desc,bgl.uptime desc', ' and classify = 2');

$gameLists = array_merge($gameMonthTop5, $appMonthTop5);
$modwns = array_column($gameLists, 'mview');
array_multisort($modwns, SORT_DESC, $gameLists);

```

#### 小编推荐、编辑推荐

```
        if ($tagInfo['gameid']) {
            $ridStr = $tagInfo['gameid'];
            //小编推荐
            if ($tagInfo['type'] == 1) {
                $editorRecom = $gameServie->getGameList($gpfield, 1, 'uptime desc', ' and bgl.id =' .$ridStr);
            } else {
                $editorRecom = $appService->getAppList($gpfield, 1, 'uptime desc', ' and bgl.id =' .$ridStr);
            }
        }
        if ($tagInfo['remark']) {
            $rid4Str =  '';
            $rid4Arr = explode(',', $tagInfo['remark']);
            //编辑推荐1
            if ($tagInfo['type'] == 1) {
                if (!empty($rid4Arr)) {
                    $rid4Str = implode(',', $rid4Arr);
                    $editorRecom4 = $gameServie->getGameList($gpfield, 4, 'uptime desc', ' and bgl.id in(' .$rid4Str .')');
                }
            } else {
                if (!empty($rid4Arr)) {
                    $rid4Str = implode(',', $rid4Arr);
                    $editorRecom4 = $appService->getAppList($gpfield, 4, 'uptime desc', ' and bgl.id in(' .$rid4Str .')');
                }
            }

        }
```

#### url转换

```
$mobileUrl = str_replace(env('app.pc.domainUrl'), env('app.mobile.domainUrl'), current_url());
$pcUrl = str_replace(env('app.mobile.domainUrl'), env('app.pc.domainUrl'), current_url());
```

#### 其他注意

```
if ($gameLists) {
    $tagfield = "id,name,catalog,img";
    foreach($gameLists as &$val) {
        //专题
        if ($val['tags']) {
            $tWhere = "status = 1 and catalog != '' and catalog is not null and id in (" . $val['tags'] . ")";
            $val['tag'] = $tagService->getNewsList($tagfield, 30, $tWhere, 0);
        }
    }
}
```
