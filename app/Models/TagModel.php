<?php

namespace App\Models;

class TagModel extends BaseModel
{

    protected $table = 'tags';

    /**
     * @function getWebListCount: 获取列表的总数
     */
    public function getTagListCount($where)
    {
        return $this->db->table($this->table)
            ->where($where)
            ->select("id")
            ->countAllResults();
    }

    public function upTagsBatch(array $updateData)
    {
        if (empty($updateData)) {
            return false;
        }

        return $this->db->table($this->table)->updateBatch($updateData, 'id');
    }

}