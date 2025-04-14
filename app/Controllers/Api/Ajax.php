<?php

namespace App\Controllers\Api;

use App\Models\NotFoundModel;
use Config\Services;
use App\Helpers\LoggerHelper;
class Ajax extends BaseController
{
    protected $log;
    public function __construct()
    {
        $this->log = new LoggerHelper();
    }
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
            $this->log->info('create path success! path:');
            //生成文件
            
            if($this->staticNewHtml($info['path'], $info['fname'], $info['content'])) {
                return Services::response()->setBody(json_encode(['msg' => '生成成功', 'code' => 200, 'data' => []]));
            } else {
                return Services::response()->setBody(json_encode(['msg' => '生成失败', 'code' => 400, 'data' => []]));
            }
        } else {
            return Services::response()->setBody(json_encode(['msg' => 'id不能为空', 'code' => 400, 'data' => []]));
        }
    }

    private function staticNewHtml($path, $file, $info)
    {
        if (!file_exists($path)) {
            $mSucess = mkdir($path, 0777, true);
            if(!$mSucess) {
                $this->log->error('mkdir path:'.$path."error!");
            }
        }
        $path .= substr($path, -1) == '/' ? '' : '/';

        if ($info){
            // 当前文件清空重新写
            @unlink($path . $file);
            $data = json_decode($info,true);
            $this->log->info('create data info:'.$info);
            foreach ($data as $key=>$val){
                if (!empty($val)){
                    $writeFileRes = $this->write_file($path . $file, print_r($val, true) . PHP_EOL, "w+"); //覆盖写
                    if (!$writeFileRes) {
                        $this->log->error('write file error! path.file:'.$path . $file);
                    }
                }
            }
        }
        $this->log->info('create path success! path:'.$path.';file:'.$file);
        return true;
    }

    function write_file($path, $data, $mode = 'wb')
    {
        if ( ! $fp = @fopen($path, $mode))
        {
            $this->log->error('fopen path error! path:'.$path);
            return FALSE;
        }

        flock($fp, LOCK_EX);

        for ($result = $written = 0, $length = strlen($data); $written < $length; $written += $result)
        {
            $result = fwrite($fp, substr($data, $written));
            if ($result === FALSE)
            {
                $this->log->error('fwrite data error! data:'.$data);
                break;
            }
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        return is_int($result);
    }
}