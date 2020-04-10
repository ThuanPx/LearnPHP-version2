<?php

namespace App\Jobs;

use App\CSV;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use League\Csv\ResultSet;

class InsertCSVJob implements ShouldQueue
{
    use Dispatchable, SerializesModels;

    private $records;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($records)
    {
        $this->records = $records;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $csvs = [];
        dd($this->records);
        foreach ($this->records as $record) {
            array_push($csvs, $record);
        }
        CSV::insert($csvs);
    }
}
