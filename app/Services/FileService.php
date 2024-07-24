<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class FileService
{

    /**
     * @throws ValidationException
     */
    public static function storeRelatedImage(array $uploadedFiles, Model $model): void
    {
        foreach ($uploadedFiles as $file) {
            $fileName = $file->hashName();
            Storage::disk('public')->put('images', $file );
            $model->images()->create(['name' => $fileName]);
        }
    }

    public static function deleteRelatedImages(Model $model): void
    {
        foreach ($model->images()->get() as $image) {
            Storage::disk('public')->delete('images/' . $image->name);
            $image->delete();
        }
    }

    /**
     * @throws ValidationException
     */
    public static function updateRelatedImage(array $uploadedFiles, Model $model): void
    {
        static::deleteRelatedImages($model);
        static::storeRelatedImage($uploadedFiles, $model);
    }
}
