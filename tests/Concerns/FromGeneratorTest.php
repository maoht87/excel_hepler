<?php

namespace Omt\ExcelHelper\Tests\Concerns;

use Generator;
use Omt\ExcelHelper\Concerns\Exportable;
use Omt\ExcelHelper\Concerns\FromGenerator;
use Omt\ExcelHelper\Tests\TestCase;

class FromGeneratorTest extends TestCase
{
    /**
     * @test
     */
    public function can_export_from_generator()
    {
        $export = new class implements FromGenerator {
            use Exportable;

            /**
             * @return Generator;
             */
            public function generator(): Generator
            {
                for ($i = 1; $i <= 2; $i++) {
                    yield ['test', 'test'];
                }
            }
        };

        $response = $export->store('from-generator-store.xlsx');

        $this->assertTrue($response);

        $contents = $this->readAsArray(__DIR__ . '/../Data/Disks/Local/from-generator-store.xlsx', 'Xlsx');

        $this->assertEquals(iterator_to_array($export->generator()), $contents);
    }
}
