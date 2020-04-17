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
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return array $model
     */
    public function createImageModels(Request $request, Model $model)
    {
        $imageModels = array_map(function ($image) use ($model) {
            return [
                'url' => storage_path('app/' . Storage::putFile('images', $image)),
                'imageable_type' => get_class($model),
                'imageable_id' => $model->id,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }, $request->file('images', []));

        return $imageModels;
    }
}
