<?php

namespace Omt\ExcelHelper\Concerns;

interface WithMultipleSheets
{
    /**
     * @return array
     */
    public function sheets(): array;
}
