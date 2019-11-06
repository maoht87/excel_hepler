<?php

namespace Omt\ExcelHelper\Tests\Data\Stubs;

use Illuminate\Support\Collection;
use Omt\ExcelHelper\Concerns\Exportable;
use Omt\ExcelHelper\Concerns\WithMultipleSheets;

class FromViewExportWithMultipleSheets implements WithMultipleSheets
{
    use Exportable;

    /**
     * @var Collection
     */
    protected $users;

    /**
     * @param Collection $users
     */
    public function __construct(Collection $users)
    {
        $this->users = $users;
    }

    /**
     * @return SheetForUsersFromView[]
     */
    public function sheets() : array
    {
        return [
            new SheetForUsersFromView($this->users->forPage(1, 100)),
            new SheetForUsersFromView($this->users->forPage(2, 100)),
            new SheetForUsersFromView($this->users->forPage(3, 100)),
        ];
    }
}
