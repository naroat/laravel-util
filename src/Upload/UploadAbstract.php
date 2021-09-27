<?php


namespace Taoran\Laravel\Upload;


use App\Exceptions\ApiException;

abstract class UploadAbstract
{
    /**
     * 文件格式验证
     *
     * @param $ext
     * @return bool
     * @throws ApiException
     */
    public function extCheck($ext)
    {
        $ext = strtolower($ext);
        $exts = ExtGroup::$ext;
        if (!in_array($ext, $exts)) {
            throw new ApiException('不支持的文件类型！');
        }
        return true;
    }

}
