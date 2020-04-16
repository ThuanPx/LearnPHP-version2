<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait ImageTrait
{

    /**
     * Create models image
     *
     * @param  \App\Http\Requests\PostFormRequest  $request
     * @return \App\Image
     */
    public function createImageModels(Request $request, Model $model)
    {
        $images = $request->file('images');
        $imageModels = [];
        if (!isset($images)) {
            return $imageModels;
        }
        foreach ($images as $image) {
            $path = Storage::putFile('images', $image);
            $imageModels[] = [
                'url' => storage_path('app/' . $path),
                'imageable_type' => get_class($model),
                'imageable_id' => $model->id,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        return $imageModels;
    }
}
