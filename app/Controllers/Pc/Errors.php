<?php

namespace App\Controllers\Pc;

class Errors extends BaseController
{
    public function show404()
    {
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");
        $info['nav'] = 0;
        return view("/Statics/Pc/errors/404.html", $info);
    }

}
