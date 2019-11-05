<?php

namespace Omt\ExcelHelper\Tests\Data\Stubs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Omt\ExcelHelper\Tests\TestCase;

class AfterQueueExportJob implements ShouldQueue
{
    use Queueable;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function handle()
    {
        TestCase::assertFileExists($this->filePath);
    }
}
