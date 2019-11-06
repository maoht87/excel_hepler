<?php

namespace Omt\ExcelHelper\Tests\Data\Stubs;

use Illuminate\Database\Query\Builder;
use Omt\ExcelHelper\Concerns\Exportable;
use Omt\ExcelHelper\Concerns\FromQuery;
use Omt\ExcelHelper\Concerns\WithEvents;
use Omt\ExcelHelper\Concerns\WithMapping;
use Omt\ExcelHelper\Events\BeforeSheet;
use Omt\ExcelHelper\Tests\Data\Stubs\Database\User;

class FromUsersQueryExportWithMapping implements FromQuery, WithMapping, WithEvents
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
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeSheet::class   => function (BeforeSheet $event) {
                $event->sheet->chunkSize(10);
            },
        ];
    }

    /**
     * @param User $row
     *
     * @return array
     */
    public function map($row): array
    {
        return [
            'name' => $row->name,
        ];
    }
}
