<?php

namespace Omt\ExcelHelper\Concerns;

use Illuminate\Database\Query\Builder;

interface FromQuery
{
    /**
     * @return Builder
     */
    public function query();
}
