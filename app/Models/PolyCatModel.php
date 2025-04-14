<?php namespace App\Models;

class PolyCatModel extends BaseModel
{

    protected $table = 'polycat_category';
    //查询返回的字段
    protected $selectFields = [
        'id', 'name', 'catalog', 'catalog_key'
    ];

    // protected $allowedFields = [
    //     'id', 'name', 'catalog', 'catalog_key', 'seo_title', 'seo_keywords', 'seo_description', 'sort_order', 'addtime', 'uptime', 'status', 'addadmin', 'upadmin'
    // ];

}