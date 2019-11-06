<?php

namespace Omt\ExcelHelper\Tests\Concerns;

use Illuminate\Support\Str;
use Omt\ExcelHelper\Concerns\Importable;
use Omt\ExcelHelper\Concerns\ToArray;
use Omt\ExcelHelper\Concerns\ToModel;
use Omt\ExcelHelper\Concerns\WithMappedCells;
use Omt\ExcelHelper\Tests\Data\Stubs\Database\User;
use Omt\ExcelHelper\Tests\TestCase;
use PHPUnit\Framework\Assert;

class WithMappedCellsTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'testing']);
    }

    /**
     * @test
     */
    public function can_import_with_references_to_cells()
    {
        $import = new class implements WithMappedCells, ToArray {
            use Importable;

            /**
             * @return array
             */
            public function mapping(): array
            {
                return [
                    'name'  => 'B1',
                    'email' => 'B2',
                ];
            }

            /**
             * @param array $array
             */
            public function array(array $array)
            {
                Assert::assertEquals([
                    'name'  => 'Patrick Brouwers',
                    'email' => 'maodk61@gmail.com',
                ], $array);
            }
        };

        $import->import('mapped-import.xlsx');
    }

    /**
     * @test
     */
    public function can_import_with_references_to_cells_to_model()
    {
        $import = new class implements WithMappedCells, ToModel {
            use Importable;

            /**
             * @return array
             */
            public function mapping(): array
            {
                return [
                    'name'  => 'B1',
                    'email' => 'B2',
                ];
            }

            /**
             * @param array $array
             *
             * @return User
             */
            public function model(array $array)
            {
                Assert::assertEquals([
                    'name'  => 'Patrick Brouwers',
                    'email' => 'maodk61@gmail.com',
                ], $array);

                $array['password'] = Str::random();

                return new User($array);
            }
        };

        $import->import('mapped-import.xlsx');

        $this->assertDatabaseHas('users', [
            'name'  => 'Patrick Brouwers',
            'email' => 'maodk61@gmail.com',
        ]);
    }
}
