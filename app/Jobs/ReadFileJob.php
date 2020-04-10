<?php

namespace App\Jobs;

use App\CSV;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;
use League\Csv\Statement;

class ReadFileJob implements ShouldQueue
{
    use Dispatchable;

    protected $path;
    protected $offset;
    private $limit = 5000;

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
        $reader->setHeaderOffset(0);

        $stmt = (new Statement())
            ->offset($this->offset)
            ->limit($this->limit);

        $records = $stmt->process($reader);

        if ($records->count() == 0) {
            unlink($this->path);
            // Import data csv success
            return;
        }
        $csvs = [];
        foreach ($records as $record) {
            $csvs[] = $record;
        }
        CSV::insert($csvs);

        $this->offset += $this->limit;
        dispatch($this);
    }
}
