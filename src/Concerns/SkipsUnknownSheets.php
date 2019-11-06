<?php

namespace Omt\ExcelHelper\Concerns;

interface SkipsUnknownSheets
{
    /**
     * @param string|int $sheetName
     */
    public function onUnknownSheet($sheetName);
}
