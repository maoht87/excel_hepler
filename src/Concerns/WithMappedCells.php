<?php

namespace Omt\ExcelHelper\Concerns;

interface WithMappedCells
{
    /**
     * @return array
     */
    public function mapping(): array;
}
