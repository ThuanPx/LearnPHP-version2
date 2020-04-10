<?php

namespace App\Jobs;

use App\CSV;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ReadFileJob implements ShouldQueue
{
    use Dispatchable;

    private $path;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(String $path)
    {
        $this->path = $path;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file = file($this->path);
        $data = array_slice($file, 1);
        $dataChunk = (array_chunk($data, 5000));
        foreach ($dataChunk as $data) {
            $values = [];
            foreach ($data as $row) {
                $values[] =  ['name' => $row[0]];
            }
            CSV::insert($values);
        }
    }
}
