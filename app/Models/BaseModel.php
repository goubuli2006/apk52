<?php 

namespace App\Models;

use CodeIgniter\Model;
use App\Helpers\VeImage;

class BaseModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    protected $primaryKey = 'id';
    protected $returnType = 'array';
    
    /**
     * @function updateRow: 添加一个数据
    */
    public function addRow($data)
    {
          $this->db->table($this->table)->insert($data);
          return $this->db->insertID();
    }

    /**
     * @function updateRow: 更新一个数据
    */
    public function updateRow($data, $id = [])
    {
        if (empty($id)) {
            return false;
        }
        return $this->db->table($this->table)->update($data, $id);
    }

    /**
     * @function getInfoById: 获取一个数据
    */
    public function getInfoById($id, $field = "*")
    {
        if (!$field) {
            $field = $this->selectFields ?? '*';
        }
        return $this->db->table($this->table)->where("id", $id)->select($field)->get()->getRowArray();
    }

    /**
     * @function getTableList: 获取表数据
    */
    public function getTableList($where = [], $field = '*', $limit = 0, $offset = 0, $order = '', $group = '')
    {
        if (!$field) {
            $field = $this->selectFields ?? '*';
        }
        return $this->db->table($this->table)
            ->where($where)
            ->select($field)
            ->limit($limit, $offset)
            ->orderBy($order)
            ->groupBy($group)
            ->get()
            ->getResultArray();
    }

    /**
     * @function getTableListWhereIn: 获取表数据
    */
    public function getTableListWhereIn($where = [], $whereIn = [], $field = '*', $limit = 0, $offset = 0, $order = '', $inField = 'id', $group = '')
    {
        if (!$field) {
            $field = $this->selectFields ?? '*';
        }
        $query = $this->db->table($this->table)
            ->select($field)
            ->where($where);
        if ($whereIn) {
            $query = $query->whereIn($inField, $whereIn);
        }
        
        return $query->limit($limit, $offset)
            ->orderBy($order)
            ->groupBy($group)
            ->get()
            ->getResultArray();
    }

    /**
     * @function getInfoByWhere: 获取单条数据
    */
    public function getOneInfoByWhere($where = [], $field = '*')
    {
        if (!$field) {
            $field = $this->selectFields ?? '*';
        }
       return  $this->db->table($this->table)->where($where)->select($field)->get()->getRowArray();
    }

    /**
     * @function getWebListCount: 获取列表的总数
     */
    public function getTableListCount($where = [], $whereIn = [], $inField = 'id', $id = 'id')
    {
        $query = $this->db->table($this->table)
            ->where($where);
        if (!empty($whereIn)) {
            $query = $query->whereIn($inField, $whereIn);
        }
        return $query->select($id)
                ->countAllResults();
    }

    /**
     * @function updateRow: 更新一个数据
    */
    public function setGoodsUpate($where)
    {
        return $this->db->table($this->table)->where($where)->set('goods', 'goods+1', false)->update();
    }
}