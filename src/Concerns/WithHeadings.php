<?php

namespace Omt\ExcelHelper\Concerns;

interface WithHeadings
{
    /**
     * @return array
     */
    public function headings(): array;
}
