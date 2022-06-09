<?php

namespace App\Service;

class SaveImageService
{

    public function store($uploads_path, $imageBase64, $extension)
    {
        $image_chunks = explode(";base64,", $imageBase64);
        $image = base64_decode($image_chunks[1]);

        $filename = md5(uniqid()) . '.' . $extension;
        $uploads_directory = $uploads_path . '/' . $filename;

        file_put_contents($uploads_directory, $image);

        return $filename;
    }
}
