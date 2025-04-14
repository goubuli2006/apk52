<?php

namespace App\Controllers\Api;

use CodeIgniter\Config\Services;

class CacheFiledel extends BaseController
{
    protected $white_open = true; //白名单是否开启

    protected $max_num = 10; //最大显示数量
    protected $del_num = 10; //不传递path 每次最多删除数量
    const WHITE_FITLE_NAME_LIST = [
        'category',
        'ploycat_category'
    ];
    /**
     * 获取目录文件列表
     * 
     */
    public function getDirFile ()
    {
        $input = $this->request->getPost();
        $path = isset($input['path']) ? $input['path'] : '';
        $type = isset($input['type']) ? $input['type'] : 2; //1 文件名 2路径
        $isMd5 = isset($input['isMd5']) ? $input['isMd5'] : false; //true: path直接md5得到文件名
        isset($input['limit']) ? $this->max_num = $input['limit'] : ''; 
        $isDir = true;
        $data = [];
        //文件名时 进行拼接
        if ($type == 1 && $path) {
            if ($isMd5) {
                $path = $input['path'] = WRITEPATH  . 'cache' . DIRECTORY_SEPARATOR . md5($path);
            } else {
                $path = $input['path'] = WRITEPATH  . 'cache' . DIRECTORY_SEPARATOR . $path;
            }
            $isDir = false;
        }

        if ($path) {
            $this->white_open = false; //传递时 去掉白名单
            if($type == 2) {
                $isDir = false;
            }
        }

        //限制writable
        if (!$this->checkData($input)) {
            return Services::response()->setBody(json_encode(['msg' => 'path error', 'code' => 400, 'data' => []]));
        }
        if (!$path) {
            $path = $this->getDefaultCacheFilePath();
        }

        if ($isDir && !$this->checkDir($path)) {
            $data['path'] = $path;
            return Services::response()->setBody(json_encode(['msg' => 'dir path not exists', 'code' => 400, 'data' => $data]));
        }

        if (!$isDir && !$this->checkFile($path) && !$this->checkDir($path)) {
            $data['path'] = $path;
            return Services::response()->setBody(json_encode(['msg' => 'file path not exists', 'code' => 400, 'data' => $data]));
        }

        $file = $this->getDir($path);
        $file = $this->formatData($file);
        return Services::response()->setBody(json_encode(['code' => 0,'msg' => 'ok', 'data' => $file]));
    }

    /**
     * Undocumented function 删除目录或者文件
     *
     */
    public function delDirOrFile ()
    {
        $input = $this->request->getPost();
        $path = isset($input['path']) ? $input['path'] : '';
        if (!$this->checkData($input)) {
            return Services::response()->setBody(json_encode(['msg' => 'path error', 'code' => 400, 'data' => []]));
        }
        if (!$path) {
            $path = $this->getDefaultCacheFilePath();
        }
        $isDir = $this->checkDir($path);
        $isFile = $this->checkFile($path);
        if ($isDir) {
            $this->delDir($path);
            return Services::response()->setBody(json_encode(['msg' => 'del dir file success!', 'code' => 0, 'data' => []]));
        }
        if ($isFile) {
            $this->delFile($path);
            return Services::response()->setBody(json_encode(['msg' => 'del file success!', 'code' => 0, 'data' => []]));
        }
        return Services::response()->setBody(json_encode(['msg' => 'file or paht not exists', 'code' => 400, 'data' => []]));
    }

    protected function formatData ($file = [])
    {   
        $res = [];
        if (!empty($file)) {
            foreach ($file as $key => $v) {
                $res[$key]['id'] = $key+1;
                $res[$key]['name'] = $v;
            }
        }
        return $res;
    }
    protected function checkData ($input)
    {
        if (isset($input['path']) && $input['path'] && (strpos($input['path'], 'writable') === false) )
        {
            return false;
        }
        return true;
    }
    protected function checkDir ($dir)
    {
        if (!is_dir($dir)) return false;
        return true;
    }

    protected function checkFile ($file)
    {
        if (!is_file($file)) return false;
        return true;
    }

    protected function getDefaultCacheFilePath ()
    {
        $path = ROOTPATH . "writable" . DIRECTORY_SEPARATOR . "cache";
        return $path;
    }

    protected function getDir($path)
    {
        if($this->checkFile($path)) {
            return [$path];
        }

        $fileItem = [];
        // $files = scandir($path); //阻塞
        static $i = 1;
        // foreach($files as $v) {
        //     $newPath = $path .DIRECTORY_SEPARATOR . $v;
        //     if(is_dir($newPath) && $v != '.' && $v != '..') {
        //         $fileItem = array_merge($fileItem, $this->getDir($newPath));
        //     }else if(is_file($newPath)){
        //         //白名单
        //         if ($this->white_open) {
        //             if (!in_array($v, self::WHITE_FITLE_NAME_LIST)) {
        //                 continue;
        //             }
        //         }
        //         if ($i > $this->max_num) {
        //             break;
        //         }
        //         $i++;
        //         $fileItem[] = $newPath;
        //     }
        // }
        if(is_dir($path)){
            $handle = opendir($path);
            //句柄读取
            while (false !== $file = readdir($handle)) {
                //如果读取到".",或".."时,则跳过
                if($file == "." || $file == ".."){
                    continue;
                }
                $newPath = $path .DIRECTORY_SEPARATOR . $file;
                //判断读到的文件名是不是目录,如果是目录,则开始递归;
                if(is_dir($newPath)){  //加上父目录再判断
                    $fileItem = array_merge($fileItem, $this->getDir($newPath));
                } else if(is_file($newPath)) {
                    if ($this->white_open) {
                        if (!in_array($file, self::WHITE_FITLE_NAME_LIST)) {
                            continue;
                        }
                    }
                    if ($i > $this->max_num) {
                        break;
                    }
                    $i++;
                    $fileItem[] = $newPath;
                }
            }
            closedir($handle);
        }
        return $fileItem;
    }

    protected function delDir($dir) 
    {
        if (!is_dir($dir)) {
            return false;
        }
        $handle = opendir($dir);
        static $i = 0;
        while (($file = readdir($handle)) !== false) {
        
            if ($file != "." && $file != "..") {
                $path = $dir . DIRECTORY_SEPARATOR . $file;
                if(is_dir($path)) {
                    $this->delDir($path);
                } else {
                    @unlink($path);
                    if ($i > $this->del_num) {
                        break;
                    }
                    $i++;
                }
            }
        
        }
        // if (readdir($handle) == false) {
            closedir($handle);
        // }
        return true;
    }

    protected function delFile($file)
    {
        @unlink($file);
    }
}