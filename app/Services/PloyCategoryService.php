<?php

namespace App\Services;
use App\Models\PolyCatModel;
use App\Traits\Functions;
use App\Helpers\CacheHelper;
use App\Helpers\CacheFacade;

class PloyCategoryService extends BaseService
{
    use Functions;

    public function __construct ()
    {
        parent::__construct();
        $this->service = new PolyCatModel();
    }
 
    public function getAllCategories()
    {
        return $this->service->getTableList(["status" => 1]);
    }

    public function getCategoryById($id)
    {
        return $this->service->getOneInfoByWhere(["id" => $id]);
    }

    public function cacheCategories ()
    {
        $res = $this->getAllCategories();
        return $this->formatCategories($res);
    }

    public function formatCategories ($data = [])
    {
        return $this->formatCateData($data);
    }

    public function getCacheAllCategories()
    {
        return CacheFacade::get(CacheHelper::POLYCAT_CATEGORY);
    }
    
    /**
     * 获取分类或者子分类
     *
     * @param [type] $id 主id
     * @param integer $childId
     * @param string $childType 子类型type 值
     * @return array
     */
    public function getCacheCategofyByIdOrCatalog($id, $childId = 0, $childType = 'id')
    {
        $res = [];
        $data = $this->getCacheAllCategories();
        if ($data) {
            foreach ($data as $item) {
                if ($item["id"] == $id) {
                    $res = $item;
                }
            }
        }
        if ($childId !== 0) {
            if (isset($res['children'])) {
                foreach ($res['children'] as $vc) {
                    if ($vc[$childType] == $childId) {
                        $res = $vc;
                    }
                }
            }
        }
        return $res;
    }

    /**
     * Undocumented function
     *
     * @param integer $id 父id
     * @param integer $cid 子id
     * @param array $data 可以直接传递seo_ data
     * @return void
     */
    public function getTdkToTemplate($id = 0, $cid = 0, $data = [])
    {
        $view           = \Config\Services::renderer();
        $info           = [
            'title'       => '',
            'description' => '',
            'keywords'    => ''
        ];
        if (!empty($data)) {
            $info = [
                'title'       => isset($data['seo_title']) ? $data['seo_title'] : '',               //  default
                'description' => isset($data['seo_description']) ? $data['seo_description'] : '',
                'keywords'    => isset($data['seo_keywords']) ? $data['seo_keywords'] : '',
            ];
        } else {
            $data = $this->getCacheCategofyByIdOrCatalog($id, $cid);
            if (!empty($data)) {
                $info['title']       = $data['seo_title'];
                $info['description'] = $data['seo_description'];
                $info['keywords']    = $data['seo_keywords'];
            }
        }
        $view->setData(['tdk' => $info]);
    }
}