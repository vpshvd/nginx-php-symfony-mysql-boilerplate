<?php

namespace App\Service\Upload;

interface UploadInterface
{
    public function upload(string $visual, string $uuid): string;
}
