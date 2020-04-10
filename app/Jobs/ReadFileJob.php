<?php

namespace App\Jobs;

use App\CSV;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use League\Csv\Reader;
use League\Csv\Statement;

class ReadFileJob implements ShouldQueue
{
    use Dispatchable;

    private $path;
    private $offset;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $path, int $offset)
    {
        $this->path = $path;
        $this->offset = $offset;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $reader = Reader::createFromPath($this->path, 'r');

        if ($this->offset == 0) $reader->setHeaderOffset(0);

        $stmt = (new Statement())
            ->offset($this->offset)
            ->limit(200);

        $records = $stmt->process($reader);

        if ($records->count() < 200) {
            // Import data csv success
            return;
        }

        $csvs = [];
        foreach ($records as $record) {
            array_push($csvs, $record);
        }
        CSV::insert($csvs);

        $nextOffset = $this->offset + 200;
        ReadFileJob::dispatch($this->path, $nextOffset);
    }
}
