<?php

namespace Omt\ExcelHelper\Tests;

use Omt\ExcelHelper\Concerns\Exportable;
use Omt\ExcelHelper\Concerns\RegistersEventListeners;
use Omt\ExcelHelper\Concerns\WithEvents;
use Omt\ExcelHelper\Events\BeforeExport;
use Omt\ExcelHelper\Events\BeforeSheet;
use Omt\ExcelHelper\Sheet;
use Omt\ExcelHelper\Writer;
use PhpOffice\PhpSpreadsheet\Document\Properties;

class DelegatedMacroableTest extends TestCase
{
    /**
     * @test
     */
    public function can_call_methods_from_delegate()
    {
        $export = new class implements WithEvents {
            use RegistersEventListeners, Exportable;

            public static function beforeExport(BeforeExport $event)
            {
                // ->getProperties() will be called via __call on the ->getDelegate()
                TestCase::assertInstanceOf(Properties::class, $event->writer->getProperties());
            }
        };

        $export->download('some-file.xlsx');
    }

    /**
     * @test
     */
    public function can_use_writer_macros()
    {
        $called = false;
        Writer::macro('test', function () use (&$called) {
            $called = true;
        });

        $export = new class implements WithEvents {
            use RegistersEventListeners, Exportable;

            public static function beforeExport(BeforeExport $event)
            {
                // call macro method
                $event->writer->test();
            }
        };

        $export->download('some-file.xlsx');

        $this->assertTrue($called);
    }

    /**
     * @test
     */
    public function can_use_sheet_macros()
    {
        $called = false;
        Sheet::macro('test', function () use (&$called) {
            $called = true;
        });

        $export = new class implements WithEvents {
            use RegistersEventListeners, Exportable;

            public static function beforeSheet(BeforeSheet $event)
            {
                // call macro method
                $event->sheet->test();
            }
        };

        $export->download('some-file.xlsx');

        $this->assertTrue($called);
    }
}
