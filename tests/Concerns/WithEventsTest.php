<?php

namespace Omt\ExcelHelper\Tests\Concerns;

use Omt\ExcelHelper\Concerns\Exportable;
use Omt\ExcelHelper\Events\AfterImport;
use Omt\ExcelHelper\Events\AfterSheet;
use Omt\ExcelHelper\Events\BeforeExport;
use Omt\ExcelHelper\Events\BeforeImport;
use Omt\ExcelHelper\Events\BeforeSheet;
use Omt\ExcelHelper\Events\BeforeWriting;
use Omt\ExcelHelper\Excel;
use Omt\ExcelHelper\Reader;
use Omt\ExcelHelper\Sheet;
use Omt\ExcelHelper\Tests\Data\Stubs\BeforeExportListener;
use Omt\ExcelHelper\Tests\Data\Stubs\CustomConcern;
use Omt\ExcelHelper\Tests\Data\Stubs\CustomSheetConcern;
use Omt\ExcelHelper\Tests\Data\Stubs\ExportWithEvents;
use Omt\ExcelHelper\Tests\Data\Stubs\ImportWithEvents;
use Omt\ExcelHelper\Tests\TestCase;
use Omt\ExcelHelper\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class WithEventsTest extends TestCase
{
    /**
     * @test
     */
    public function export_events_get_called()
    {
        $event = new ExportWithEvents();

        $eventsTriggered = 0;

        $event->beforeExport = function ($event) use (&$eventsTriggered) {
            $this->assertInstanceOf(BeforeExport::class, $event);
            $this->assertInstanceOf(Writer::class, $event->getWriter());
            $eventsTriggered++;
        };

        $event->beforeWriting = function ($event) use (&$eventsTriggered) {
            $this->assertInstanceOf(BeforeWriting::class, $event);
            $this->assertInstanceOf(Writer::class, $event->getWriter());
            $eventsTriggered++;
        };

        $event->beforeSheet = function ($event) use (&$eventsTriggered) {
            $this->assertInstanceOf(BeforeSheet::class, $event);
            $this->assertInstanceOf(Sheet::class, $event->getSheet());
            $eventsTriggered++;
        };

        $event->afterSheet = function ($event) use (&$eventsTriggered) {
            $this->assertInstanceOf(AfterSheet::class, $event);
            $this->assertInstanceOf(Sheet::class, $event->getSheet());
            $eventsTriggered++;
        };

        $this->assertInstanceOf(BinaryFileResponse::class, $event->download('filename.xlsx'));
        $this->assertEquals(4, $eventsTriggered);
    }

    /**
     * @test
     */
    public function import_events_get_called()
    {
        $event = new ImportWithEvents();

        $eventsTriggered = 0;

        $event->beforeImport = function ($event) use (&$eventsTriggered) {
            $this->assertInstanceOf(BeforeImport::class, $event);
            $this->assertInstanceOf(Reader::class, $event->getReader());
            $eventsTriggered++;
        };

        $event->afterImport = function ($event) use (&$eventsTriggered) {
            $this->assertInstanceOf(AfterImport::class, $event);
            $this->assertInstanceOf(Reader::class, $event->getReader());
            $eventsTriggered++;
        };

        $event->beforeSheet = function ($event) use (&$eventsTriggered) {
            $this->assertInstanceOf(BeforeSheet::class, $event);
            $this->assertInstanceOf(Sheet::class, $event->getSheet());
            $eventsTriggered++;
        };

        $event->afterSheet = function ($event) use (&$eventsTriggered) {
            $this->assertInstanceOf(AfterSheet::class, $event);
            $this->assertInstanceOf(Sheet::class, $event->getSheet());
            $eventsTriggered++;
        };

        $event->import('import.xlsx');
        $this->assertEquals(4, $eventsTriggered);
    }

    /**
     * @test
     */
    public function can_have_invokable_class_as_listener()
    {
        $event = new ExportWithEvents();

        $event->beforeExport = new BeforeExportListener(function ($event) {
            $this->assertInstanceOf(BeforeExport::class, $event);
            $this->assertInstanceOf(Writer::class, $event->getWriter());
        });

        $this->assertInstanceOf(BinaryFileResponse::class, $event->download('filename.xlsx'));
    }

    /**
     * @test
     */
    public function can_have_global_event_listeners()
    {
        $event = new class {
            use Exportable;
        };

        $beforeExport = false;
        Writer::listen(BeforeExport::class, function () use (&$beforeExport) {
            $beforeExport = true;
        });

        $beforeWriting = false;
        Writer::listen(BeforeWriting::class, function () use (&$beforeWriting) {
            $beforeWriting = true;
        });

        $beforeSheet = false;
        Sheet::listen(BeforeSheet::class, function () use (&$beforeSheet) {
            $beforeSheet = true;
        });

        $afterSheet = false;
        Sheet::listen(AfterSheet::class, function () use (&$afterSheet) {
            $afterSheet = true;
        });

        $this->assertInstanceOf(BinaryFileResponse::class, $event->download('filename.xlsx'));

        $this->assertTrue($beforeExport, 'Before export event not triggered');
        $this->assertTrue($beforeWriting, 'Before writing event not triggered');
        $this->assertTrue($beforeSheet, 'Before sheet event not triggered');
        $this->assertTrue($afterSheet, 'After sheet event not triggered');
    }

    /**
     * @test
     */
    public function can_have_custom_concern_handlers()
    {
        // Add a custom concern handler for the given concern.
        Excel::extend(CustomConcern::class, function (CustomConcern $exportable, Writer $writer) {
            $writer->getSheetByIndex(0)->append(
                $exportable->custom()
            );
        });

        $exportWithConcern = new class implements CustomConcern {
            use Exportable;

            public function custom()
            {
                return [
                    ['a', 'b'],
                ];
            }
        };

        $exportWithConcern->store('with-custom-concern.xlsx');
        $actual = $this->readAsArray(__DIR__ . '/../Data/Disks/Local/with-custom-concern.xlsx', 'Xlsx');
        $this->assertEquals([
            ['a', 'b'],
        ], $actual);

        $exportWithoutConcern = new class {
            use Exportable;
        };

        $exportWithoutConcern->store('without-custom-concern.xlsx');
        $actual = $this->readAsArray(__DIR__ . '/../Data/Disks/Local/without-custom-concern.xlsx', 'Xlsx');

        $this->assertEquals([[null]], $actual);
    }

    /**
     * @test
     */
    public function can_have_custom_sheet_concern_handlers()
    {
        // Add a custom concern handler for the given concern.
        Excel::extend(CustomSheetConcern::class, function (CustomSheetConcern $exportable, Sheet $sheet) {
            $sheet->append(
                $exportable->custom()
            );
        }, AfterSheet::class);

        $exportWithConcern = new class implements CustomSheetConcern {
            use Exportable;

            public function custom()
            {
                return [
                    ['c', 'd'],
                ];
            }
        };

        $exportWithConcern->store('with-custom-concern.xlsx');
        $actual = $this->readAsArray(__DIR__ . '/../Data/Disks/Local/with-custom-concern.xlsx', 'Xlsx');
        $this->assertEquals([
            ['c', 'd'],
        ], $actual);

        $exportWithoutConcern = new class {
            use Exportable;
        };

        $exportWithoutConcern->store('without-custom-concern.xlsx');
        $actual = $this->readAsArray(__DIR__ . '/../Data/Disks/Local/without-custom-concern.xlsx', 'Xlsx');

        $this->assertEquals([[null]], $actual);
    }
}
