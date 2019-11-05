<?php

namespace Omt\ExcelHelper\Tests\Data\Stubs;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Omt\ExcelHelper\Concerns\Exportable;
use Omt\ExcelHelper\Concerns\FromQuery;
use Omt\ExcelHelper\Concerns\WithCustomChunkSize;

class FromNonEloquentQueryExport implements FromQuery, WithCustomChunkSize
{
    use Exportable;

    /**
     * @return Builder
     */
    public function query()
    {
        return DB::table('users')->select('name')->orderBy('id');
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 10;
    }
}
