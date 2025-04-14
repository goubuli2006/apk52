<?php namespace App\Services;

use App\Models\CategoryModel;
use App\Enum\CategoryEnum;
use App\Enum\CommonEnum;
use App\Traits\Functions;
use App\Services\RedisService;

class CategoryService extends BaseService
{
    use Functions;
    public function __construct()
    {
        parent::__construct();
        $this->service = new CategoryModel();
    }
    
    public function getAllCates() 
    {
        return $this->service->getTableList(["status" => 1]);
    }

    public function getCacheCateList()
    {
        $data = $this->getAllCates();
        return $this->formatCateData($data);
    }

    public function getTdkToTemplate($pid, $cid = "",  $childType = 'catalog')
    {
        $data = $this->getCacheCateList();
        $tdk = [];
        foreach($data as $val) {
            if ($val['id'] == $pid) {
                $tdk['title'] = $val['seo_title'];
                $tdk['keywords'] = $val['seo_keywords'];
                $tdk['description'] = $val['seo_description'];
                if ($cid !== "" && !empty($val['children'])) {
                    foreach($val['children'] as $vc) {
                        if ($vc[$childType] == $cid) {
                            $tdk['title'] = $vc['seo_title'];
                            $tdk['keywords'] = $vc['seo_keywords'];
                            $tdk['description'] = $vc['seo_description'];
                        }
                    }
                }
            }
        }
        return $tdk;
    }
    public function getInfoByCatalog($pid, $catalog = "", $childType = 'catalog', $cateData = [])
    {
        $data =  $cateData ? : $this->getCacheCateList();
        $info = [];
        foreach($data as $val) {
            if ($val['id'] == $pid) {
                $info = $val;
                if ($catalog !== "" && !empty($val['children'])) {
                    foreach($val['children'] as $vc) {
                        if ($vc[$childType] == $catalog) {
                            $info = $vc;
                            break;
                        } else {
                            $info = [];
                        }
                    }
                }
            }
        }
        return $info;
    }
    
    public function getGameChildrenTdk($pid =  1)
    {
        if ($pid == CategoryEnum::GAME_CATE_PID) {
            $gameAllCatesOrigin = $this->getInfoByCatalog(CategoryEnum::GAME_CATE_PID)['children'] ? : [];
            $AllCatesCatalog = array_column($gameAllCatesOrigin, 'catalog', 'id');
        } else {
            $gameAllCatesOrigin = $this->getInfoByCatalog(CategoryEnum::SOFT_CATE_PID)['children'] ? : [];
            $AllCatesCatalog = array_column($gameAllCatesOrigin, 'catalog', 'id');
        }
        if (empty($AllCatesCatalog)) return [];
        $tdk = [];
        foreach($gameAllCatesOrigin as $v) {
            $tdk[$v['id']]['title'] = $v['seo_title'];
            $tdk[$v['id']]['keywords'] = $v['seo_keywords'];
            $tdk[$v['id']]['description'] = $v['seo_description'];
        }
        return $tdk;
    }
    public function getFormatData($list)
    {
        $gameTypeData = (new CategoryService())->getInfoByCatalog(CategoryEnum::GAME_CATE_PID);
        $softTypeData = (new CategoryService())->getInfoByCatalog(CategoryEnum::SOFT_CATE_PID);

        foreach($list as $key => $val) {
            if ($val['classify'] == CommonEnum::CLASSIFY_GAME_ID) {
                $list[$key]['href'] = env('app.domainUrl') . $gameTypeData['catalog'] . "/" . $val['catalog'] . "/";
            } else {
                $list[$key]['href'] = env('app.domainUrl') . $softTypeData['catalog'] . "/" . $val['catalog'] . "/";
            }
        }
        return $list;
    }
    /**
     * 树形结构
     */
    function getAllCateData($menuData, $parent_id = 0)
    {
        $tree = [];
        foreach ($menuData as &$item) {
            if ($item["pid"] == $parent_id) {
                $item['id'] = $item['id'];
                $item['title'] = $item['name'];
                $item["children"] = $this->getAllCateData($menuData, $item["id"]);
                if ($item["children"]) {
                    $item['spread'] = 1;
                } else {
                    $item['spread'] = 0;
                }
                array_push($tree, $item);
            }
        }
        return $tree;
    }
}