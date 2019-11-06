<?php

namespace Omt\ExcelHelper\Concerns;

interface WithValidation
{
    /**
     * @return array
     */
    public function rules(): array;
}
