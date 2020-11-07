<?php


namespace Taoran\Laravel\Upload;

use App\Exceptions\ApiException;
use Illuminate\Http\UploadedFile;
use mysql_xdevapi\Exception;

class Upload extends UploadAbstract
{
    /**
     * 上传驱动
     *
     * local, aliyun
     * @var bool|string
     */
    public $drive = 'local';

    /**
     * 文件对象
     *
     * @var
     */
    public $file;

    /**
     * 上传类
     *
     * @var
     */
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
        if (request()->hasFile('file') && request()->file('file')->isValid()) {
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
        //根据mime类型获取扩展名
        $ext = $this->file->guessExtension();

        //验证 - 扩展名
        $this->extCheck($ext);

        //文件大小
        $filesize = $this->file->getClientSize();

        //php.ini中配置的上传文件的最大大小
        $maxFileSize = $this->file->getMaxFilesize();

        if ($filesize > $maxFileSize) {
            throw new ApiException('上传失败，文件过大！');
        }

        //自定义文件名
        //$filename = md5(time() . rand(1000, 9999)) . '.' . $ext;

        //上传文件
        $this->upload($this->file);
    }
}