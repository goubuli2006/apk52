<?php namespace App\Models;

class AdvertpicModel extends BaseModel
{
    //广告位详情
    protected $table = 'advertpic';

    protected $selectFields = [
        'id', 'adid', 'describe', 'url', 'img', 'remark', 'status', 'is_del', 'type'
    ];

}