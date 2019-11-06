<?php

namespace Omt\ExcelHelper\Tests\Data\Stubs;

use Omt\ExcelHelper\Concerns\Importable;
use Omt\ExcelHelper\Concerns\WithEvents;
use Omt\ExcelHelper\Events\AfterImport;
use Omt\ExcelHelper\Events\AfterSheet;
use Omt\ExcelHelper\Events\BeforeImport;
use Omt\ExcelHelper\Events\BeforeSheet;

class ImportWithEvents implements WithEvents
{
    use Importable;

    /**
     * @var callable
     */
    public $beforeImport;

    /**
     * @var callable
     */
    public $beforeSheet;

    /**
     * @var callable
     */
    public $afterSheet;

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeImport::class => $this->beforeImport ?? function () {
            },
            AfterImport::class => $this->afterImport ?? function () {
            },
            BeforeSheet::class => $this->beforeSheet ?? function () {
            },
            AfterSheet::class => $this->afterSheet ?? function () {
            },
        ];
    }
}
