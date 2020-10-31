<?php


namespace Taoran\Laravel\Upload;

use mysql_xdevapi\Exception;

class Upload extends UploadAbstract
{
    public $drive = 'local';  //local, aliyun

    public $file;       //file

    public $upload;

    public function __construct($drive = false)
    {
        if (!empty($drive)) {
            $this->drive = $drive;
        }
        $this->setFile();
        $this->getDrive($this->drive);
    }

    public function setFile()
    {
        if (request()->hasFile('file')) {
            $this->file = request()->file('file');
        } else {
            throw new Exception('文件错误!');
        }
    }

    public function getDrive($drive)
    {
        $drive = '\Taoran\Laravel\Upload\\' . ucfirst($drive) . '\Upload';

        if (class_exists($drive)) {
            $this->upload = new $drive();
        } else {
            throw new Exception('上传失败!');
        }
    }

    public function upload()
    {
        //验证

        //文件大小验证
        dd(request()->file('file'));

        //文件格式验证

        //

    }
}