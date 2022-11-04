<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ImageHelper
{

    public static function saveImage($imageFromRequest, $diskName)
    {
        $extension = $imageFromRequest->getClientOriginalExtension();
        $imageFullName = uniqid() . time() . rand(10, 1000000) . '.' . $extension;
        Storage::disk($diskName)->put($imageFullName, File::get($imageFromRequest));
        return $imageFullName;
    }
    public static function deleteImage($imageName, $diskName)
    {
        //dd($imageName);
        Storage::disk($diskName)->delete($imageName);
    }
    public static function saveImageFromBase64($base64Image, $diskName)
    {
        $image_parts = explode(";base64,", $base64Image);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $decoded_file = base64_decode($image_parts[1]); // decode the file
        $f = finfo_open();
        $mime_type = finfo_buffer($f, $decoded_file, FILEINFO_MIME_TYPE); // extract mime type
        $extension = self::mime2ext($mime_type); // extract extension from mime type
        $fileName = uniqid() . '.' . $extension; // rename file as a unique name
        //return $file;
        Storage::disk($diskName)->put($fileName, $decoded_file);
        return $fileName;
    }
    public static function mime2ext($file_mime_type)
    {
        if ($file_mime_type == 'image/jpeg' || $file_mime_type == 'image/jpg')
            return 'jpeg';
        else if ($file_mime_type == 'image/png')
            return 'png';
        else if ($file_mime_type == 'image/gif')
            return 'gif';
        else
            return 'other';
    }
}
