<?php

namespace App\Controllers\Sitemap;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;
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

    public function game(): ResponseInterface 
    { 
        return $this->generateXmlResponse($this->generateUrlsByType('game')); 
    }

    public function app(): ResponseInterface
    {
        return $this->generateXmlResponse($this->generateUrlsByType('app'));
    }

    public function subclass(): ResponseInterface
    {
        return $this->generateXmlResponse($this->generateUrlsByType('subclass'));
    }

    public function all(): ResponseInterface
    {
        $allUrls = array_merge(
            $this->generateUrlsByType('game'),
            $this->generateUrlsByType('app'),
            $this->generateUrlsByType('subclass')
        );

        $chunkSize = 10000;
        $chunks = array_chunk($allUrls, $chunkSize);
        $sitemapIndexXml = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemapIndexXml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($chunks as $i => $chunk) {
            $sitemapFilename = "sitemap" . ($i + 1) . ".xml";
            $sitemapPath = FCPATH . $sitemapFilename;

            // generate and save each sitemap chunk
            file_put_contents($sitemapPath, $this->generateXml($chunk));

            // add to index
            $sitemapIndexXml .= '<sitemap>';
            $sitemapIndexXml .= '<loc>' . base_url($sitemapFilename) . '</loc>';
            $sitemapIndexXml .= '</sitemap>';
        }

        $sitemapIndexXml .= '</sitemapindex>';
        file_put_contents(FCPATH . 'sitemap_index.xml', $sitemapIndexXml);

        // Optionally return the index XML as response
        return response()
            ->setStatusCode(200)
            ->setContentType('application/xml')
            ->setBody($sitemapIndexXml);
    }

    private function generateXmlResponse(array $urls): ResponseInterface
    {
        $xml = $this->generateXml($urls);
        return $this->respondWithXml($xml);
    }

    private function generateUrlsByType(string $type): array
    {
        helper('text');
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

        $model = $type === 'game' ? new GameListModel() : new AppListModel();
        $service = $type === 'game' ? new GameService() : new AppService();
        $cateService = new CategoryService();
        $classify = $type === 'game' ? 1 : 2;
        $where = "status = 1 and state = 1 and classify = {$classify}";
        $typePrefix = "{$type}_";

        // 1. Main pagination
        $urls[] = base_url($type);
        $totalRows = $model->getTableListCount($where);
        $totalPages = ceil($totalRows / $this->limit);
        for ($i = 2; $i <= $totalPages; $i++) {
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
        $categories = $cateService->getInfoByCatalog($cateEnum);
        $children = $categories['children'] ?? [];

        foreach ($children as $cat) {
            $catalog = $cat['catalog'];
            $urls[] = base_url("{$type}/{$typePrefix}{$catalog}");

            $catInfo = $cateService->getInfoByCatalog($cateEnum, $catalog);
            if ($catInfo) {
                $catId = $catInfo['id'];
                $catWhere = "{$where} and type='{$catId}'";
                $catTotal = $model->getTableListCount($catWhere);
                $catPages = ceil($catTotal / $this->limit);
                for ($i = 2; $i <= $catPages; $i++) {
                    $urls[] = base_url("{$type}/{$typePrefix}{$catalog}/{$i}");
                }
            }
        }

        return $urls;
    }

    private function generateXml(array $urls): string
    {
        array_unshift($urls, base_url());

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($url, ENT_XML1) . '</loc>';
            // $xml .= '<lastmod>' . date('Y-m-d') . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.7</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return $xml;
    }

    private function respondWithXml(string $xml): ResponseInterface
    {
        return response()
            ->setStatusCode(200)
            ->setContentType('application/xml')
            ->setHeader('Cache-Control', 'public, max-age=7200')
            ->setHeader('Content-Disposition', 'attachment; filename="sitemap.xml"')
            ->setBody($xml);
    }

    private function respondWithCachedFile(string $filePath): ResponseInterface
    {
        $cachedXml = file_get_contents($filePath);
        return $this->respondWithXml($cachedXml);
    }
}
