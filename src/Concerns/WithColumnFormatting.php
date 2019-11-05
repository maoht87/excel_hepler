<?php

namespace Omt\ExcelHelper\Concerns;

interface WithColumnFormatting
{
    /**
     * @return array
     */
    public function columnFormats(): array;
}
