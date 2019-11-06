<?php

namespace Omt\ExcelHelper\Tests\Data\Stubs;

use Omt\ExcelHelper\Concerns\Exportable;
use Omt\ExcelHelper\Concerns\WithTitle;

class WithTitleExport implements WithTitle
{
    use Exportable;

    /**
     * @return string
     */
    public function title(): string
    {
        return 'given-title';
    }
}
