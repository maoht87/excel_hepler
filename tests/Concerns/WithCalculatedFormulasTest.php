<?php

namespace Omt\ExcelHelper\Tests\Concerns;

use Illuminate\Database\Eloquent\Model;
use Omt\ExcelHelper\Concerns\Importable;
use Omt\ExcelHelper\Concerns\ToArray;
use Omt\ExcelHelper\Concerns\ToModel;
use Omt\ExcelHelper\Concerns\WithCalculatedFormulas;
use Omt\ExcelHelper\Concerns\WithStartRow;
use Omt\ExcelHelper\Tests\TestCase;
use PHPUnit\Framework\Assert;

class WithCalculatedFormulasTest extends TestCase
{
    /**
     * @test
     */
    public function by_default_does_not_calculate_formulas()
    {
        $import = new class implements ToArray {
            use Importable;

            public $called = false;

            /**
             * @param array $array
             */
            public function array(array $array)
            {
                $this->called = true;

                Assert::assertSame('=1+1', $array[0][0]);
            }
        };

        $import->import('import-formulas.xlsx');

        $this->assertTrue($import->called);
    }

    /**
     * @test
     */
    public function can_import_to_array_with_calculated_formulas()
    {
        $import = new class implements ToArray, WithCalculatedFormulas {
            use Importable;

            public $called = false;

            /**
             * @param array $array
             */
            public function array(array $array)
            {
                $this->called = true;

                Assert::assertSame(2, $array[0][0]);
            }
        };

        $import->import('import-formulas.xlsx');

        $this->assertTrue($import->called);
    }

    /**
     * @test
     */
    public function can_import_to_model_with_calculated_formulas()
    {
        $import = new class implements ToModel, WithCalculatedFormulas {
            use Importable;

            public $called = false;

            /**
             * @param array $row
             *
             * @return Model|null
             */
            public function model(array $row)
            {
                $this->called = true;

                Assert::assertSame(2, $row[0]);

                return null;
            }
        };

        $import->import('import-formulas.xlsx');

        $this->assertTrue($import->called);
    }

    public function can_import_with_formulas_and_reference()
    {
        $import = new class implements ToModel, WithCalculatedFormulas, WithStartRow {
            use Importable;

            public $called = false;

            /**
             * @param array $row
             *
             * @return Model|null
             */
            public function model(array $row)
            {
                $this->called = true;

                Assert::assertSame('julien', $row[1]);

                return null;
            }

            public function startRow(): int
            {
                return 2;
            }
        };

        $import->import('import-external-reference.xls');

        $this->assertTrue($import->called);
    }
}
