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
        $sitemapIndexPath = FCPATH . 'sitemap_index.xml';

        if (file_exists($sitemapIndexPath) && filemtime($sitemapIndexPath) > strtotime('-2 days')) {
            return $this->respondWithXml(file_get_contents($sitemapIndexPath), 'sitemap_index.xml', true);
        }

        $newUrls = array_merge(
            $this->generateUrlsByType('game'),
            $this->generateUrlsByType('app'),
            $this->generateUrlsByType('subclass')
        );

        $existingUrls = [];

        // Check existing sitemap files (e.g., sitemap1.xml, sitemap2.xml, etc.)
        // 获取已存在的sitemap文件
        $existingSitemaps = $this->getExistingSitemaps();
        
        foreach ($existingSitemaps as $sitemapFile) {
            // Extract URLs from the existing sitemap
            // 从已存在的sitemap中提取URL
            $existingUrls = array_merge($existingUrls, $this->extractUrlsFromSitemap($sitemapFile));
        }

        // Filter out already existing URLs
        // 过滤掉已存在的URL
        $newUrls = array_diff($newUrls, $existingUrls);

        if (empty($newUrls)) {
            // No new URLs, so just serve the existing sitemap index
            // 没有新URL，只需提供现有的sitemap索引
            return $this->respondWithXml(file_get_contents($sitemapIndexPath), 'sitemap_index.xml', true);
        }

        $chunkSize = 10000;
        $chunks = array_chunk($newUrls, $chunkSize);
        $existingCount = $this->countExistingSitemaps();

        $sitemapIndexXml = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemapIndexXml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($chunks as $i => $chunk) {
            $sitemapFilename = "sitemap" . ($existingCount + $i + 1) . ".xml";
            $sitemapFilePath = FCPATH . $sitemapFilename;

            if (!file_exists($sitemapFilePath) || $i + 1 > $existingCount) {
                file_put_contents($sitemapFilePath, $this->generateXml($chunk, $sitemapFilename));
            }

            $sitemapIndexXml .= '<sitemap>';
            $sitemapIndexXml .= '<loc>' . base_url($sitemapFilename) . '</loc>';
            $sitemapIndexXml .= '</sitemap>';
        }

        $sitemapIndexXml .= '</sitemapindex>';
        file_put_contents($sitemapIndexPath, $sitemapIndexXml);

        // Serve the new index and redirect user to it
        return $this->respondWithXml($sitemapIndexXml, 'sitemap_index.xml', true);
    }

    private function countExistingSitemaps(): int
    {
        $count = 0;
        while (file_exists(FCPATH . 'sitemap' . ($count + 1) . '.xml')) {
            $count++;
        }
        return $count;
    }

    // Method to get a list of existing sitemaps (e.g., sitemap1.xml, sitemap2.xml)
    private function getExistingSitemaps(): array
    {
        // List of existing sitemaps in the directory (you may need to adjust this path)
        return glob(FCPATH . 'sitemap*.xml');
    }

    private function generateXmlResponse(array $urls): ResponseInterface
    {
        $xml = $this->generateXml($urls);
        return $this->respondWithXml($xml);
    }

    private function extractUrlsFromSitemap(string $sitemapFile): array
    {
        $urls = [];

        // Load the sitemap XML
        $sitemapXml = \simplexml_load_file($sitemapFile);
        
        if ($sitemapXml) {
            // Extract all <loc> elements (URLs)
            foreach ($sitemapXml->url as $url) {
                $urls[] = (string)$url->loc; // Convert SimpleXML object to string
            }
        }

        return $urls;
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

    private function generateXml(array $urls, string $filename): string
    {
        if ($filename === 'sitemap1.xml') {
            array_unshift($urls, base_url());
        }

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

    private function respondWithXml(string $xml, string $filename = 'sitemap.xml', bool $redirectToView = false): ResponseInterface
    {
        // Add HTML redirect if needed
        if ($redirectToView) {

            $scheme = $this->request->getUri()->getScheme();
            $host = $this->request->getUri()->getHost();

            $html = <<<HTML
            <!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="refresh" content="1; url={$scheme}://{$host}/{$filename}">
            </head>
            <body>
                <p>Sitemap generated. <a href="{$scheme}://{$host}/{$filename}">Click here</a> if not redirected.</p>
            </body>
            </html>
            HTML;
            return response()->setBody($html)->setContentType('text/html');
        }

        return response()
            ->setStatusCode(200)
            ->setContentType('application/xml')
            ->setHeader('Cache-Control', 'public, max-age=7200')
            ->setBody($xml);
    }

}
