<?php namespace App\Models;

class AdvertModel extends BaseModel
{

    protected $table = 'advert';

    /**
     * @function getWebListCount: 获取列表的总数
     */
    public function getAdvertListCount($where)
    {
        return $this->editorDb->table($this->table)
            ->where($where)
            ->select("id")
            ->countAllResults();
    }
    
}