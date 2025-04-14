<?php 

namespace App\Models;

class CategoryModel extends BaseModel
{
    protected $table = 'category';
    protected $allowedFields = ['id', 'name', 'catalog', 'seo_title', 'seo_keywords', 'seo_description', 'pid', 'sort_order', 'addtime', 'uptime', 'status', 'addadmin', 'upadmin'];

    /**
     * @function getTableList: 获取表数据
    */
    public function getTableLists($where = [], $field = '*', $limit = 0, $offset = 0, $order = 'sort_order desc', $group = '')
    {
        if (!$field) {
            $field = $this->selectFields ?? '*';
        }
        $defaultWhere = [
            'status' => 1
        ];
        $where = array_merge($defaultWhere, $where);
        return $this->getTableList($where, $field, $limit, $offset, $order, $group);
    }

    /**
     * @function findByParentId: 获取某一个id下面是否有子分类
     */
    public function findByParentId($id)
    {
        return $this->editorDb->table($this->table)->where("pid", $id)
            ->select($this->allowedFields)->get()->getRowArray();
    }

    /**
     * @function getCategoryListByPid: 获取pid = 0得数据
     */
    public function getCategoryListByPid($pid)
    {
        return $this->editorDb->table($this->table)
            ->where("status", 1)
            ->where("pid", $pid)
            ->select($this->allowedFields)
            ->orderBy("sort_order desc")->get()->getResultArray();
    }

    /**
     * list传入数组返回type_name, catalog, 上一级的拼接uri, game/jcsc
     */
    public function getListCategoryTypeName ($list = [], $resName = 'type_name', $keyName = 'type')
    {
        if (empty($list)) return $list;
        $cateList = $this->getTableList([], 'id,name,catalog,pid');
        $cateList = array_column($cateList, null, 'id');
        // dd($cateList);
        //一维数组 ['type']
        if (count($list) == count($list, COUNT_RECURSIVE)) {
            if (isset($list[$keyName]) && array_key_exists($list[$keyName], $cateList)) {
                $list[$resName] = $cateList[$list[$keyName]]['name'];
                $list['catalog'] = $cateList[$list[$keyName]]['catalog'];
                if (array_key_exists($cateList[$list[$keyName]]['pid'], $cateList)) {
                    $list['uri'] = $cateList[$cateList[$list[$keyName]]['pid']]['catalog'] . '/' . $cateList[$list[$keyName]]['catalog'];
                } else {
                    $list['uri'] = '';
                }
            } else {
                $list[$resName] = '';
                $list['catalog'] = '';
                $list['uri'] = '';
            }
        } else {
            foreach ($list as &$val) {
                if (isset($val[$keyName]) && array_key_exists($val[$keyName], $cateList)) {
                    $val['type_name'] = $cateList[$val[$keyName]]['name'];
                    $val['catalog'] = $cateList[$val[$keyName]]['catalog'];
                    if (array_key_exists($cateList[$val[$keyName]]['pid'], $cateList)) {
                        $val['uri'] = $cateList[$cateList[$val[$keyName]]['pid']]['catalog'] . '/' . $cateList[$val[$keyName]]['catalog'];
                    } else {
                        $val['uri'] = '';
                    }
                } else {
                    $val[$resName] = '';
                    $val['catalog'] = '';
                    $val['uri'] = '';
                }
            
            }
        }
        return $list;
    }
}



