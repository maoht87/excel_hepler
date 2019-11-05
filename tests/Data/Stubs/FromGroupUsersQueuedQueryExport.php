<?php

namespace Omt\ExcelHelper\Tests\Data\Stubs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Builder;
use Omt\ExcelHelper\Concerns\Exportable;
use Omt\ExcelHelper\Concerns\FromQuery;
use Omt\ExcelHelper\Concerns\WithCustomChunkSize;
use Omt\ExcelHelper\Concerns\WithMapping;
use Omt\ExcelHelper\Tests\Data\Stubs\Database\Group;

class FromGroupUsersQueuedQueryExport implements FromQuery, WithCustomChunkSize, ShouldQueue, WithMapping
{
    use Exportable;

    /**
     * @return Builder
     */
    public function query()
    {
        return Group::first()->users();
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->name,
            $row->email,
        ];
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 10;
    }
}
