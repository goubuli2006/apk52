<?php

namespace App\Helpers;
/**
 * 可以灵活定制的分页类
 * 可以定制的选项包括，分页链接显示效果，当前页码链接按钮的样式，URL中获取分页值的名字，可以随意带自己的参数
 * @author 严颖，星空幻颖
 * @link http://www.bidianer.com
 */
class Pagination
{
    /**
     * @var int $totalRows 数据库查询得到的总记录条数
     */
    private $totalRows;

    /**
     * @var int $pageSize 网站每一页显示的列表条数
     */
    private $pageSize = 20;

    /**
     * @var string $route 当前页面的 URL 地址
     */
    private $route;

    /**
     * @var int $pageAmount 计算出来的总的页码数
     */
    private $pageAmount;

    /**
     * @var int $currentPage 当前页码
     */
    private $currentPage;

    /**
     * @var int $offset 页码的左右偏移量。比如当前页码为5，则在5的左右各显示几个数字链接，默认为3个，则效果为2,3,4,5,6,7,8
     */
    private $offset = 4;

    /**
     * @var string $pageParam URL中当前页码的参数名称。通过$_GET['page']获取当前页码时候的名字，默认为page。
     * 例如：http://bidianer.com?page=2
     */
    private $pageParam = 'page';

    /**
     * @var string $activeClassName 当前页码链接的高亮类名
     */
    private $activeClassName = "active";

    /**
     * @var string $indexPageLabel 首页链接的文字提示
     */
    private $indexPageLabel = '首页';

    /**
     * @var string $prevPageLabel 上一页链接的文字提示
     */
    private $prevPageLabel = '上一页';

    /**
     * @var string $nextPageLabel 下一页链接的文字提示
     */
    private $nextPageLabel = '下一页';

    /**
     * @var string $endPageLabel 最后一页链接的文字提示
     */
    private $endPageLabel = '尾页';

    /**
     * @var string $class 分页链接最外层的div的类
     */
    private $class = 'pagination';

    /**
     * 初始化分页类
     * 同时需要设置一些必填的参数
     * @param $param
     */
    public function __construct($param)
    {
        $param['currentPage'] = isset($_GET[$this->pageParam]) ? intval($_GET[$this->pageParam]) : 1;
        $this->getConfig($param);
        $this->checkCurrentPage();
    }

    /**
     * 获取配置
     * @param array $config
     * @throws Exception
     * @return void
     */
    private function getConfig($config)
    {
        if (!is_array($config))
        {
            throw new Exception('配置选项必须为数组');
        }

        foreach ($config as $key => $value)
        {
            if(isset($config[$key]) && $config[$key])
            {
                $this->$key = $value;
            }
        }
    }

    /**
     * 创建分页链接
     * @param int $style 默认为 1 ：获取链接全部组件
     * $style == 2 ：仅获取数字链接
     * $style == 3 ：仅获取上一页，下一页
     * $style == 4 ：仅获取上一页、下一页、数字链接，不包含首尾页
     *
     * @param boolean $output 为TRUE时，返回分页链接
     * @param boolean $output 为FALSE时，直接输出分页链接
     * @return mixed
     */
    public function pagination($style = 1,$output=TRUE)
    {
        $this->getBaseRoute();

        //获取全部组件
        if($style == '1')
        {
            $page = $this->indexPage().$this->prevPage().$this->pageNumber().$this->nextPage().$this->endPage();
        }
        else if($style == '2')
        {
            //获取纯数字链接
            $page = $this->pageNumber();
        }
        else if($style == '3')
        {
            //只获取上一页下一页
            $page = $this->prevPage().$this->nextPage();
        }
        else if($style =='4')
        {
            //上一页、下一页、数字链接
            $page = $this->prevPage().$this->pageNumber().$this->nextPage();
        }

        if($output)
        {
            return '<div class="'.$this->class.'">'.$page.'</div>';
        }
        else
        {
            echo '<div class="'.$this->class.'">'.$page.'</div>';
        }
    }

    /**
     * 获取每页的记录数量
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * 获取数据库检索出来的数据总数
     */
    public function getTotalRows()
    {
        return $this->totalRows;
    }

    /**
     * 获取当前页码
     * @return int 当前页码，经过真伪判断的
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * 计算出所有的页数
     * 可以类外面直接调用此方法返回页码总数
     * @return int 页码的总数
     */
    public function getPageAmount()
    {
        $this->pageAmount = ceil( $this->totalRows / $this->pageSize);
        if($this->pageAmount <= 0)
        {
            $this->pageAmount = 1;
        }
        return $this->pageAmount;
    }

    /**
     * 获取用户当前URL的基准链接
     * 1、如果链接携带参数，则在链接之后加&page=
     * 2、如果不携带参数，则直接加?page=
     * @return boolean
     */
    private function getBaseRoute()
    {
        $currentUrl = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $urlArr = explode('?' , $currentUrl);
        $prevUrl = $urlArr['0'];
        $queryString = $_SERVER['QUERY_STRING'];
        if(!$queryString)
        {
            $this->route = $prevUrl.'?'.$this->pageParam.'=';
            return false;
        }

        $queryArr = explode('&' , $queryString);
        $paramString = array();
        foreach ($queryArr as $value)
        {
            $param = explode('=' , $value);
            if($param['0'] != $this->pageParam)
            {
                $paramString[] = implode('=',$param);
            }
        }
        $baseQueryString = implode('&' , $paramString);
        if($baseQueryString){
            $this->route = $prevUrl.'?'.$baseQueryString.'&'.$this->pageParam.'=';
        }else{
            $this->route = $prevUrl.'?'.$this->pageParam.'=';
        }
    }

    /**
     * 验证当前页码的真伪性
     * 如果当前页码小于1或者没有，则默认当前页码为1
     * 如果当前页码大于页码总数，则默认当前页码为页码总数
     */
    private function checkCurrentPage()
    {
        $this->getPageAmount();
        if($this->currentPage < 1 || !$this->currentPage)
        {
            $this->currentPage = 1;
        }
        else if($this->currentPage > $this->pageAmount)
        {
            $this->currentPage = $this->pageAmount;
        }
    }

    /**
     * 获取首页链接
     * @return string|boolean
     */
    private function indexPage()
    {
        if($this->currentPage == 1) return false;
        return '<a href="'.$this->route.'1" data-page="1">'.$this->indexPageLabel.'</a>';
    }

    /**
     * 获取尾页链接
     * @return boolean|string
     */
    private function endPage()
    {
        if($this->currentPage == $this->pageAmount) return false;
        return '<a href="'.$this->route.$this->pageAmount.'" data-page="'.$this->pageAmount.'">'.$this->endPageLabel.'</a>';
    }

    /**
     * 获取上一页的链接
     * @return string|boolean
     */
    private function prevPage()
    {
        if($this->currentPage == 1) return false;
        return '<a href="'.$this->route.( $this->currentPage - 1 ).'" data-page="'.($this->currentPage - 1).'">'.$this->prevPageLabel.'</a>';
    }

    /**
     * 获取下一页的链接
     * @return string|boolean
     */
    private function nextPage()
    {
        if($this->currentPage == $this->pageAmount) return false;
        return '<a href="'.$this->route.( $this->currentPage + 1 ).'">'.$this->nextPageLabel.'</a>';
    }

    /**
     * 获取中间数字页码的链接
     * @return string
     */
    private function pageNumber()
    {
        $left ="";
        $right = "";

        // 如果总记录的条数“大于”所有链接的数量时候
        if($this->pageAmount > ($this->offset * 2 + 1))
        {
            //当前页码距离首页的距离
            $leftNum = $this->currentPage - 1;

            //当前页码距离尾页的距离
            $rightNum = $this->pageAmount - $this->currentPage;

            //当当前页码距离首页距离不足偏移量offset时候，在右边补齐缺少的小方块
            if( $leftNum < $this->offset)
            {
                //左边的链接
                for($i = $leftNum; $i >= 1 ; $i--)
                {
                    $left .= '<a href="'.$this->route.( $this->currentPage - $i ).'" data-page="'.( $this->currentPage - $i ).'">'.( $this->currentPage - $i ).'</a>';
                }

                //右边的链接
                for($j = 1; $j <= ($this->offset * 2 - $leftNum); $j++)
                {
                    $right .= '<a href="'.$this->route.( $this->currentPage + $j ).'" data-page="'.( $this->currentPage + $j ).'">'.( $this->currentPage + $j ).'</a>';
                }
            }
            else if($rightNum < $this->offset)
            {
                //左边的链接
                for($i = ($this->offset * 2 - $rightNum); $i >= 1 ; $i--)
                {
                    $left .= '<a href="'.$this->route.( $this->currentPage - $i ).'" data-page="'.( $this->currentPage - $i ).'">'.( $this->currentPage - $i ).'</a>';
                }

                //右边的链接
                for($j = 1; $j <= $rightNum; $j++)
                {
                    $right .= '<a href="'.$this->route.( $this->currentPage + $j ).'" data-page="'.( $this->currentPage + $j ).'">'.( $this->currentPage + $j ).'</a>';
                }
            }
            else
            {
                //当前链接左边的链接
                for($i = $this->offset; $i >= 1 ; $i--)
                {
                    $left .= '<a href="'.$this->route.( $this->currentPage - $i ).'" data-page="'.( $this->currentPage - $i ).'">'.( $this->currentPage - $i ).'</a>';
                }

                //当前链接右边的链接
                for($j = 1; $j <= $this->offset; $j++)
                {
                    $right .= '<a href="'.$this->route.( $this->currentPage + $j ).'" data-page="'.( $this->currentPage + $j ).'">'.( $this->currentPage + $j ).'</a>';
                }
            }

            return $left.'<a href="'.$this->route.$this->currentPage.'" class="'.$this->activeClassName.'">'.$this->currentPage.'</a>'.$right;
        }
        else
        {
            $allLink='';
            //当页码总数小于需要显示的链接数量时候，则全部显示出来
            for($j = 1; $j <= $this->pageAmount; $j++)
            {
                 $allLink.='<a href="'.$this->route.$j.'" data-page="'.$j.'" class="'.($j == $this->currentPage ? $this->activeClassName:'').'">'.$j.'</a>';
            }
            return $allLink;
        }
    }
}