<?php

namespace Omt\ExcelHelper\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class QueueImport implements ShouldQueue
{
    use ExtendedQueueable, Dispatchable;

    public function handle()
    {
        //
    }
}
