<?php

namespace App\Libraries\Page;
use CodeIgniter\Pager\Pager as dPager;

class Pager1
{
    protected $page_max = 2;
    protected $total = 0;
    protected $limit = 5;
    protected $p = 'page';
    protected $split = '';

    public function __construct($total, $limit)
    {
        $this->page_max = ceil($total / $limit);
        $this->total = $total;
        $this->limit = $limit;
    }

    /**
     * 显示页码
     */
    public function show()
    {

        $page_max = $this->page_max;
        // $url = $_SERVER['PHP_SELF'];
        $url = $_SERVER['REQUEST_URI'];
        $urlArr = explode('?', $url);
        $url = $_SERVER['REQUEST_SCHEME']. '://'. $_SERVER['HTTP_HOST'] . $urlArr[0];
        $param = $this->getParam();

        $p = isset($_GET[$this->p]) ? intval($_GET[$this->p]) : 1;
        $p = $p < 1 ? 1 : $p;
        $p = $p > $page_max ? $page_max : $p;

        echo '<div class="php_page pagecode" >';

        if ($p > 1) {
            $last_page = $p - 1;
            echo "<a href='$url?{$this->p}=$last_page{$param}'>上页</a>";
            echo $this->split;
        }

        if ($p == 1) {
            echo '<span class="php_page_current current"><a class="current">1</a></span>';
        } else {
            echo "<a href='$url?{$this->p}=1{$param}'>1</a>";
        }
        echo $this->split;

        $start = $this->getStart($p);
        $end = $this->getEnd($p);

        if ($start > 2) {
            echo '...';
            echo $this->split;
        }

        for ($i = $start; $i <= $end; $i++) {
            if ($p == $i) {
                echo "<span class='php_page_current'><a class='current'>" . $i . '</a></span>';
            } else {
                echo "<a href='$url?{$this->p}={$i}{$param}'>" . $i . '</a>';
            }
            echo $this->split;
        }
        if ($end < $page_max - 1) {
            echo '...';
            echo $this->split;
        }

        if ($page_max > 1) {
            if ($p == $page_max) {
                echo "<span class='php_page_current'><a class='current'>$page_max</a></span>";
            } else {
                echo "<a href='$url?{$this->p}={$page_max}{$param}'>$page_max</a>";
            }
            echo $this->split;
        }

        if ($p < $page_max) {
            $next_page = $p + 1;
            echo "<a href='$url?{$this->p}=$next_page{$param}'>下页</a>";
            echo $this->split;
        }

        // echo '<span class="php_page_info">';
        // echo $this->total . ' 条数据,当前第 ' . $p . ' 页,共 ' . $page_max . ' 页';
        // echo '</span>';

        echo '</div>';
    }

    /**
     * 自定义页码参数
     * @param $val
     */
    public function setP($val)
    {
        $this->p = $val;
    }

    /**
     * 获取queryString
     * @return string
     */
    private function getParam()
    {
        $query_str = $_SERVER['QUERY_STRING'];
        if (!$query_str) {
            return '';
        }
        $query_arr = explode('&', $query_str);

        $param_arr = array();
        foreach ($query_arr as $query_item) {
            $item = explode('=', $query_item);
            $key = $item[0];
            $value = $item[1];
            $param_arr[$key] = $key . '=' . $value;
        }

        unset($param_arr[$this->p]);
        if (empty($param_arr)) {
            return '';
        }
        $param = implode('&', $param_arr);
        return '&' . $param;
    }

    /**
     * 获取起始页码
     * @param int $p
     * @return int
     */
    private function getStart($p)
    {
        if ($p < 9) {
            return 2;
        } else {
            return $p > $this->page_max - 8 ? $this->page_max - 8 : $p;
        }
    }

    /**
     * 获取最后一页
     * @param int $p
     * @return int
     */
    private function getEnd($p)
    {
        if ($p < 9) {
            $end = 9;
            return $end > $this->page_max - 1 ? $this->page_max - 1 : $end;
        } else {
            $end = $p + 7;
            return $end > $this->page_max - 1 ? $this->page_max - 1 : $end;
        }
    }
}