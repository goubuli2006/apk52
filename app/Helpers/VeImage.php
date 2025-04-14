<?php
namespace App\Helpers;

class VeImage
{
    protected $imageDomain = 'http://itopdog.oscaches.com/';

    public function dealVeImage ($filePath)
    {
        $res = '';
        if ($filePath) {
            if (strpos($filePath, 'http') !== false) {
                return $filePath;
            }
            $domain = env('app.volc.uploadDomain') ?? $this->imageDomain;
            return $domain . $filePath;
        }
        return $res;
    }
}