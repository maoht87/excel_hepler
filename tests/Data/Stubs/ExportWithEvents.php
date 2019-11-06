<?php

namespace Omt\ExcelHelper\Tests\Data\Stubs;

use Omt\ExcelHelper\Concerns\Exportable;
use Omt\ExcelHelper\Concerns\WithEvents;
use Omt\ExcelHelper\Events\AfterSheet;
use Omt\ExcelHelper\Events\BeforeExport;
use Omt\ExcelHelper\Events\BeforeSheet;
use Omt\ExcelHelper\Events\BeforeWriting;

class ExportWithEvents implements WithEvents
{
    use Exportable;

    /**
     * @var callable
     */
    public $beforeExport;

    /**
     * @var callable
     */
    public $beforeWriting;

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
            BeforeExport::class  => $this->beforeExport ?? function () {
            },
            BeforeWriting::class => $this->beforeWriting ?? function () {
            },
            BeforeSheet::class   => $this->beforeSheet ?? function () {
            },
            AfterSheet::class    => $this->afterSheet ?? function () {
            },
        ];
    }
}
