<?php

namespace App\Http\Controllers;

use App\Http\Requests\CSVFormRequest;
use App\Jobs\ReadFileJob;

class FileController extends Controller
{
    /**
     * Function uploadFile
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadFile(CSVFormRequest $request)
    {
        $path = $request->csv->storeAs('files', date('ymd_His') . '.csv');

        ReadFileJob::dispatch(storage_path('app/' . $path), 0);

        return response()->baseResponseStatusCreated(trans('messages.up_load_file_success'));
    }
}
