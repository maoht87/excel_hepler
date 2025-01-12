<?php

namespace Omt\ExcelHelper\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Omt\ExcelHelper\Concerns\WithChunkReading;
use Omt\ExcelHelper\Concerns\WithCustomValueBinder;
use Omt\ExcelHelper\Concerns\WithEvents;
use Omt\ExcelHelper\Events\ImportFailed;
use Omt\ExcelHelper\Files\TemporaryFile;
use Omt\ExcelHelper\Filters\ChunkReadFilter;
use Omt\ExcelHelper\HasEventBus;
use Omt\ExcelHelper\Imports\HeadingRowExtractor;
use Omt\ExcelHelper\Sheet;
use Omt\ExcelHelper\Transactions\TransactionHandler;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use Throwable;

class ReadChunk implements ShouldQueue
{
    use Queueable, HasEventBus;

    /**
     * @var WithChunkReading
     */
    private $import;

    /**
     * @var IReader
     */
    private $reader;

    /**
     * @var TemporaryFile
     */
    private $temporaryFile;

    /**
     * @var string
     */
    private $sheetName;

    /**
     * @var object
     */
    private $sheetImport;

    /**
     * @var int
     */
    private $startRow;

    /**
     * @var int
     */
    private $chunkSize;

    /**
     * @param  WithChunkReading  $import
     * @param  IReader  $reader
     * @param  TemporaryFile  $temporaryFile
     * @param  string  $sheetName
     * @param  object  $sheetImport
     * @param  int  $startRow
     * @param  int  $chunkSize
     */
    public function __construct(WithChunkReading $import, IReader $reader, TemporaryFile $temporaryFile, string $sheetName, $sheetImport, int $startRow, int $chunkSize)
    {
        $this->import        = $import;
        $this->reader        = $reader;
        $this->temporaryFile = $temporaryFile;
        $this->sheetName     = $sheetName;
        $this->sheetImport   = $sheetImport;
        $this->startRow      = $startRow;
        $this->chunkSize     = $chunkSize;
    }

    /**
     * @param  TransactionHandler  $transaction
     *
     * @throws \Omt\ExcelHelper\Exceptions\SheetNotFoundException
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function handle(TransactionHandler $transaction)
    {
        if ($this->sheetImport instanceof WithCustomValueBinder) {
            Cell::setValueBinder($this->sheetImport);
        }

        $headingRow = HeadingRowExtractor::headingRow($this->sheetImport);

        $filter = new ChunkReadFilter(
            $headingRow,
            $this->startRow,
            $this->chunkSize,
            $this->sheetName
        );

        $this->reader->setReadFilter($filter);
        $this->reader->setReadDataOnly(true);
        $this->reader->setReadEmptyCells(false);

        $spreadsheet = $this->reader->load(
            $this->temporaryFile->sync()->getLocalPath()
        );

        $sheet = Sheet::byName(
            $spreadsheet,
            $this->sheetName
        );

        if ($sheet->getHighestRow() < $this->startRow) {
            $sheet->disconnect();

            return;
        }

        $transaction(function () use ($sheet) {
            $sheet->import(
                $this->sheetImport,
                $this->startRow
            );

            $sheet->disconnect();
        });
    }

    /**
     * @param  Throwable  $e
     */
    public function failed(Throwable $e)
    {
        if ($this->import instanceof WithEvents) {
            $this->registerListeners($this->import->registerEvents());
            $this->raise(new ImportFailed($e));

            if (method_exists($this->import, 'failed')) {
                $this->import->failed($e);
            }
        }
    }
}
