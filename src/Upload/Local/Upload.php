<?php


namespace Taoran\Laravel\Upload\Local;


use Taoran\Laravel\Exception\ApiException;
use Taoran\Laravel\Upload\UploadInterface;

class Upload implements UploadInterface
{
    /**
     * 上传
     *
     * @param $file
     * @param $filename
     * @return mixed
     */
    public function upload($file, $path, $filename, $acl)
    {
        try {
            $result = $file->move($path, $filename);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage());
        }
        //返回路径 + 文件名
        return '/' . $path . '/' . $filename;
    }
}
