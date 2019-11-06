<?php

namespace Omt\ExcelHelper\Concerns;

use Omt\ExcelHelper\Row;

interface OnEachRow
{
    /**
     * @param Row $row
     */
    public function onRow(Row $row);
}
