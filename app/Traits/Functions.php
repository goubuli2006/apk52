<?php
namespace App\Traits;

trait Functions
{
    /**
     * 遍历数据 生成树状结构
     * @param $items
     * @param string $pk
     * @return array
     */
    protected function generateTree($items, $pk='id')
    {
        $tree = [];
        $items = array_column($items, null, $pk);
        foreach ($items as $k => $v) {
            if (isset($items[$v['pid']]))  $items[$v['pid']]['children'][$v['id']] = &$items[$k];
            else  $tree[] = &$items[$k];
        }
        return $tree;
    }
    public function formatCateData($menuData, $parent_id = 0)
    {
        $tree = [];
        foreach ($menuData as &$item) {
            if ($item["pid"] == $parent_id) {
                $item['id'] = $item['id'];
                $item['title'] = $item['name'];
                $item['catalog_url'] = 'xxx';
                $item["children"] = $this->formatCateData($menuData, $item["id"]);
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

?>