<?php

namespace App\Http\Controllers;

use App\Http\Requests\CSVFormRequest;
use App\Jobs\ReadFileJob;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Function uploadFile
     *
     * @return JsonResponse
     */
    public function uploadFile(CSVFormRequest $request)
    {
        $path = $request->file('csv')->getRealPath();
        ReadFileJob::dispatch($path);
        return response() -> baseResponseStatusCreated(trans('messages.up_load_file_success'));
    }
}
