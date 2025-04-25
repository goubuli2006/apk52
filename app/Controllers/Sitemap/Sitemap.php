<?php

namespace App\Controllers\Sitemap;

use CodeIgniter\Controller;
use App\Services\CategoryService;
use App\Services\GameService;
use App\Services\AppService;
use App\Models\GameListModel;
use App\Models\AppListModel;
use App\Models\TagModel;
use App\Enum\CategoryEnum;

class Sitemap extends Controller
{
    protected $limit = 40;

    public function game() { 
        return $this->generateXmlResponse($this->generateUrlsByType('game')); 
    }

    public function app()
    {
        return $this->generateXmlResponse($this->generateUrlsByType('app'));
    }

    public function subclass()
    {
        return $this->generateXmlResponse($this->generateUrlsByType('subclass'));
    }

    private function generateUrlsByType($type)
    {
        helper('text');
        $limit = $this->limit;
        $urls = [];

        if ($type === 'subclass') {
            $urls[] = base_url('subclass');
            $urls[] = base_url('subclass/game');
            $urls[] = base_url('subclass/app');

            $tagModel = new TagModel();
            $tags = $tagModel->select('catalog')->where('status', 1)->findAll();
            foreach ($tags as $tag) {
                $urls[] = base_url('subclass/' . $tag['catalog']);
            }

            return $urls;
        }

        // game or app
        $urls[] = base_url($type);

        $cateService = new CategoryService();
        $model = $type === 'game' ? new GameListModel() : new AppListModel();
        $service = $type === 'game' ? new GameService() : new AppService();

        // 1. Main pagination
        $classify = $type === 'game' ? 1 : 2;
        $where = "status = 1 and state = 1 and classify = {$classify}";
        $totalRows = $model->getTableListCount($where);
        $totalPages = ceil($totalRows / $limit);
        for ($i = 1; $i <= $totalPages; $i++) {
            $urls[] = base_url("{$type}/{$i}");
        }
        
        // 2. Detail + download pages
        $items = $type === 'game' 
            ? $service->getGameList('union_id', 100000, 'bgl.id desc', ' and classify = 1') 
            : $service->getAppList('union_id', 100000, 'bgl.id desc', ' and classify = 2');
        foreach ($items as $item) {
            $union = $item['union_id'];
            $urls[] = base_url("{$type}/{$union}");
            $urls[] = base_url("{$type}/{$union}/download");
        }

        // 3. Category + paginated category pages
        $cateEnum = $type === 'game' ? CategoryEnum::GAME_CATE_PID : CategoryEnum::SOFT_CATE_PID;
        $typePrefix = "{$type}_";
        $categories = $cateService->getInfoByCatalog($cateEnum);
        $children = isset($categories['children']) ? $categories['children'] : [];

        foreach ($children as $cat) {
            $catalog = $cat['catalog'];
            $urls[] = base_url("{$type}/{$typePrefix}{$catalog}");

            $catInfo = $cateService->getInfoByCatalog($cateEnum, $catalog);
            if ($catInfo) {
                $catId = $catInfo['id'];
                $catWhere = "{$where} and type='{$catId}'";
                $catTotal = $model->getTableListCount($catWhere);
                $catPages = ceil($catTotal / $limit);
                for ($i = 1; $i <= $catPages; $i++) {
                    $urls[] = base_url("{$type}/{$typePrefix}{$catalog}/{$i}");
                }
            }
        }

        return $urls;
    }

    public function all()
    {
        $allUrls = array_merge(
            $this->generateUrlsByType('game'),
            $this->generateUrlsByType('app'),
            $this->generateUrlsByType('subclass')
        );

        return $this->generateXmlResponse($allUrls);
    }

    private function generateXmlResponse(array $urls)
    {
        $start = microtime(true); // Start timer

        array_unshift($urls, base_url());
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($url, ENT_XML1) . '</loc>';
            // $xml .= '<lastmod>' . date('Y-m-d') . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.8</priority>';
            $xml .= '</url>';
        }
        $xml .= '</urlset>';

        // sitemap save path
        $savePath = WRITEPATH . '../public/sitemap.xml'; 
        file_put_contents($savePath, $xml);
    
        $totalUrls = count($urls);
        $end = microtime(true);
        $duration = round($end - $start, 4);
    
        return response()
            ->setStatusCode(200)
            ->setContentType('application/xml')
            ->setBody($xml);
    }
}
