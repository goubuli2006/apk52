<?php 
namespace App\Models;
class AppModel extends BaseModel
{
    protected $table = 'app';

    /**
     * 获取主游戏关联的主库 一对多
     *
     * @param [type] $fid
     * @param integer $classify
     * @param string $field
     * @return array
     */
    public function getMasterSimilarGameByIdList($fid, $classify = 1, $field = "*")
    {
        return   $this->db->table('similar_app')
            ->select($field)
            ->where(['fid' => $fid, 'classify' => $classify])
            ->get()
            ->getResultArray();
    }

    /**
     * 获取主库关联游戏 不关联similar
     *
     * @param string $where
     * @param string $field
     * @param integer $limit
     * @param integer $offset
     * @param string $order
     * @param string $group
     * @return array
     */
    public function getMasterGameListNoSimilar(string $where = "", $field = ['*'], $limit = 0, $offset = 0, $order = '', $group = 'bg.id')
    {
        return $this->db->table('app as bg')
            ->join('app_list as bgl', 'bg.id=bgl.gameid')
            ->select($field)
            ->where("bg.status = 1 and bg.state = 1 and  bgl.status=1 and bgl.state=1 ")
            ->where($where?:[])
            ->groupBy($group)
            ->orderBy($order)
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

}