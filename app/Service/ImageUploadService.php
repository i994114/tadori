<?php

namespace App\Service;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ImageUploadService
{
    //画像アップローダ：ランダムファイル名生成、ファイル保存し、ファイル名を返す
    public function processImageUpload($img) {
        //画像ファイル用にランダム名前を生成
        $fileName =  Str::uuid() . '.' . $img->getClientOriginalExtension();

        // 画像をリサイズしてバイナリデータ化
        $image = Image::make($img)->resize(64, 64)->encode();

        // ファイルをpublicディスクに保存
        Storage::disk('public')->put('uploads/' . $fileName, $image);


        return $fileName;

    }

    //ストレージに保存されたファイルを削除する
    public function deleteImage($file)
    {

        $filePath = 'uploads/' . $file;
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }

}
