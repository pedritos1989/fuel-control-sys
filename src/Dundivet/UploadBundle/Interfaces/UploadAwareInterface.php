<?php

namespace Dundivet\UploadBundle\Interfaces;


interface UploadAwareInterface
{
    public function preUpload();
    public function upload();
    public function removeUpload();
}
