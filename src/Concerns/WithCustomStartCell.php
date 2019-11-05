<?php

namespace Omt\ExcelHelper\Concerns;

interface WithCustomStartCell
{
    /**
     * @return string
     */
    public function startCell(): string;
}
