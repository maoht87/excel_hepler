<?php

namespace Omt\ExcelHelper\Tests\Data\Stubs;

use Illuminate\Database\Query\Builder;
use Omt\ExcelHelper\Concerns\Exportable;
use Omt\ExcelHelper\Concerns\FromQuery;
use Omt\ExcelHelper\Concerns\WithCustomChunkSize;
use Omt\ExcelHelper\Tests\Data\Stubs\Database\User;

class FromUsersQueryExport implements FromQuery, WithCustomChunkSize
{
    use Exportable;

    /**
     * @return Builder
     */
    public function query()
    {
        return User::query();
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 10;
    }
}
