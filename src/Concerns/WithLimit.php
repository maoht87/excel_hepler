<?php

namespace Omt\ExcelHelper\Concerns;

interface WithLimit
{
    /**
     * @return int
     */
    public function limit(): int;
}
