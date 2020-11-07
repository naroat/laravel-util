<?php


namespace Taoran\Laravel\Upload\Local;


use Taoran\Laravel\Upload\UploadInterface;

class Upload implements UploadInterface
{
    public function upload($file)
    {
        $path = $file->store('images');

        return $path;
    }

    public function download()
    {

    }

}