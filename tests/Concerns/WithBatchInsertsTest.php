<?php

namespace Omt\ExcelHelper\Tests\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Omt\ExcelHelper\Concerns\Importable;
use Omt\ExcelHelper\Concerns\ToModel;
use Omt\ExcelHelper\Concerns\WithBatchInserts;
use Omt\ExcelHelper\Tests\Data\Stubs\Database\Group;
use Omt\ExcelHelper\Tests\Data\Stubs\Database\User;
use Omt\ExcelHelper\Tests\TestCase;

class WithBatchInsertsTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'testing']);
        $this->loadMigrationsFrom(dirname(__DIR__) . '/Data/Stubs/Database/Migrations');
    }

    /**
     * @test
     */
    public function can_import_to_model_in_batches()
    {
        DB::connection()->enableQueryLog();

        $import = new class implements ToModel, WithBatchInserts {
            use Importable;

            /**
             * @param array $row
             *
             * @return Model|null
             */
            public function model(array $row)
            {
                return new User([
                    'name'     => $row[0],
                    'email'    => $row[1],
                    'password' => 'secret',
                ]);
            }

            /**
             * @return int
             */
            public function batchSize(): int
            {
                return 2;
            }
        };

        $import->import('import-users.xlsx');

        $this->assertCount(1, DB::getQueryLog());
        DB::connection()->disableQueryLog();

        $this->assertDatabaseHas('users', [
            'name'  => 'Patrick Brouwers',
            'email' => 'maodk61@gmail.com',
        ]);

        $this->assertDatabaseHas('users', [
            'name'  => 'Taylor Otwell',
            'email' => 'taylor@laravel.com',
        ]);
    }

    /**
     * @test
     */
    public function can_import_to_model_in_batches_bigger_file()
    {
        DB::connection()->enableQueryLog();

        $import = new class implements ToModel, WithBatchInserts {
            use Importable;

            /**
             * @param array $row
             *
             * @return Model|null
             */
            public function model(array $row)
            {
                return new Group([
                    'name' => $row[0],
                ]);
            }

            /**
             * @return int
             */
            public function batchSize(): int
            {
                return 1000;
            }
        };

        $import->import('import-batches.xlsx');

        $this->assertCount(5000 / $import->batchSize(), DB::getQueryLog());
        DB::connection()->disableQueryLog();
    }

    /**
     * @test
     */
    public function can_import_multiple_different_types_of_models_in_single_to_model()
    {
        DB::connection()->enableQueryLog();

        $import = new class implements ToModel, WithBatchInserts {
            use Importable;

            /**
             * @param array $row
             *
             * @return Model|Model[]|null
             */
            public function model(array $row)
            {
                $user = new User([
                    'name'     => $row[0],
                    'email'    => $row[1],
                    'password' => 'secret',
                ]);

                $group = new Group([
                    'name' => $row[0],
                ]);

                return [$user, $group];
            }

            /**
             * @return int
             */
            public function batchSize(): int
            {
                return 2;
            }
        };

        $import->import('import-users.xlsx');

        // Expected 2 batch queries, 1 for users, 1 for groups
        $this->assertCount(2, DB::getQueryLog());

        $this->assertEquals(2, User::count());
        $this->assertEquals(2, Group::count());

        DB::connection()->disableQueryLog();
    }

    /**
     * @test
     */
    public function has_timestamps_when_imported_in_batches()
    {
        $import = new class implements ToModel, WithBatchInserts {
            use Importable;

            /**
             * @param array $row
             *
             * @return Model|Model[]|null
             */
            public function model(array $row)
            {
                return new User([
                    'name'     => $row[0],
                    'email'    => $row[1],
                    'password' => 'secret',
                ]);
            }

            /**
             * @return int
             */
            public function batchSize(): int
            {
                return 2;
            }
        };

        $import->import('import-users.xlsx');

        $user = User::first();

        $this->assertNotNull($user->created_at);
        $this->assertNotNull($user->updated_at);
    }
}
