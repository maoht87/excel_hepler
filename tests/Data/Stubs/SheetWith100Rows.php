<?php

namespace Omt\ExcelHelper\Tests\Data\Stubs;

use Illuminate\Support\Collection;
use Omt\ExcelHelper\Concerns\Exportable;
use Omt\ExcelHelper\Concerns\FromCollection;
use Omt\ExcelHelper\Concerns\RegistersEventListeners;
use Omt\ExcelHelper\Concerns\ShouldAutoSize;
use Omt\ExcelHelper\Concerns\WithEvents;
use Omt\ExcelHelper\Concerns\WithTitle;
use Omt\ExcelHelper\Events\BeforeWriting;
use Omt\ExcelHelper\Tests\TestCase;
use Omt\ExcelHelper\Writer;

class SheetWith100Rows implements FromCollection, WithTitle, ShouldAutoSize, WithEvents
{
    use Exportable, RegistersEventListeners;

    /**
     * @var string
     */
    private $title;

    /**
     * @param string $title
     */
    public function __construct(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        $collection = new Collection;
        for ($i = 0; $i < 100; $i++) {
            $row = new Collection();
            for ($j = 0; $j < 5; $j++) {
                $row[] = $this->title() . '-' . $i . '-' . $j;
            }

            $collection->push($row);
        }

        return $collection;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * @param BeforeWriting $event
     */
    public static function beforeWriting(BeforeWriting $event)
    {
        TestCase::assertInstanceOf(Writer::class, $event->writer);
    }
}
