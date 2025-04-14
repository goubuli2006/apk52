<?php

namespace App\Controllers\Mobile;

use App\Models\NotFoundModel;
use Config\Services;

class Ajax extends BaseController
{
    public function createNotFound()
    {
        $input = $this->request->getPost();

        $id = $input['id'] ?? 0;
        if ($id > 0) {
            $notFoundModel = new NotFoundModel();
            $info = $notFoundModel->getOneInfoByWhere("id={$id}", "*");
            if (empty($info)) {
                return Services::response()->setBody(json_encode(['msg' => 'id错误', 'code' => 400, 'data' => []]));
            }

            //生成文件
            $this->staticNewHtml($info['path'], $info['fname'], $info['content']);
            return Services::response()->setBody(json_encode(['msg' => '生成成功', 'code' => 200, 'data' => []]));
        } else {
            return Services::response()->setBody(json_encode(['msg' => 'id不能为空', 'code' => 400, 'data' => []]));
        }
    }

    private function staticNewHtml($path, $file, $info)
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $path .= substr($path, -1) == '/' ? '' : '/';

        if ($info){
            // 当前文件清空重新写
            @unlink($path . $file);
            $data = json_decode($info,true);

            foreach ($data as $key=>$val){
                if (!empty($val)){
                    $this->write_file($path . $file, print_r($val, true) . PHP_EOL, "a+");
                }
            }
        }

        return true;
    }

    function write_file($path, $data, $mode = 'wb')
    {
        if ( ! $fp = @fopen($path, $mode))
        {
            return FALSE;
        }

        flock($fp, LOCK_EX);

        for ($result = $written = 0, $length = strlen($data); $written < $length; $written += $result)
        {
            if (($result = fwrite($fp, substr($data, $written))) === FALSE)
            {
                break;
            }
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        return is_int($result);
    }
}