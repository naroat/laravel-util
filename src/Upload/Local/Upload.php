<?php


namespace Taoran\Laravel\Upload\Local;


use Taoran\Laravel\Upload\UploadInterface;

class Upload implements UploadInterface
{
    public function upload()
    {
        dd('local');
    }

    public function download()
    {

    }

}