<?php

namespace Omt\ExcelHelper\Concerns;

interface WithStartRow
{
    /**
     * @return int
     */
    public function startRow(): int;
}
