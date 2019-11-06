<?php

namespace Omt\ExcelHelper\Concerns;

use Omt\ExcelHelper\Validators\Failure;

interface SkipsOnFailure
{
    /**
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures);
}
