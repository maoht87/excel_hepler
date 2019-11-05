<?php

namespace Omt\ExcelHelper\Tests\Data\Stubs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Omt\ExcelHelper\Concerns\Exportable;
use Omt\ExcelHelper\Concerns\WithMultipleSheets;

class ShouldQueueExport implements WithMultipleSheets, ShouldQueue
{
    use Exportable;

    /**
     * @return SheetWith100Rows[]
     */
    public function sheets(): array
    {
        return [
            new SheetWith100Rows('A'),
            new SheetWith100Rows('B'),
            new SheetWith100Rows('C'),
        ];
    }
}
