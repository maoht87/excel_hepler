<?php

namespace Omt\ExcelHelper;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;
use Omt\ExcelHelper\Console\ExportMakeCommand;
use Omt\ExcelHelper\Console\ImportMakeCommand;
use Omt\ExcelHelper\Files\Filesystem;
use Omt\ExcelHelper\Files\TemporaryFileFactory;
use Omt\ExcelHelper\Mixins\DownloadCollection;
use Omt\ExcelHelper\Mixins\StoreCollection;
use Omt\ExcelHelper\Transactions\TransactionHandler;
use Omt\ExcelHelper\Transactions\TransactionManager;

class ExcelServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            if ($this->app instanceof LumenApplication) {
                $this->app->configure('excel');
            } else {
                $this->publishes([
                    $this->getConfigFile() => config_path('excel.php'),
                ], 'config');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->mergeConfigFrom(
            $this->getConfigFile(),
            'excel'
        );

        $this->app->bind(TransactionManager::class, function () {
            return new TransactionManager($this->app);
        });

        $this->app->bind(TransactionHandler::class, function () {
            return $this->app->make(TransactionManager::class)->driver();
        });

        $this->app->bind(TemporaryFileFactory::class, function () {
            return new TemporaryFileFactory(
                config('excel.temporary_files.local_path', config('excel.exports.temp_path', storage_path('framework/excel-helper'))),
                config('excel.temporary_files.remote_disk')

            );
        });

        $this->app->bind(Filesystem::class, function () {
            return new Filesystem($this->app->make('filesystem'));
        });

        $this->app->bind('excel', function () {
            return new Excel(
                $this->app->make(Writer::class),
                $this->app->make(QueuedWriter::class),
                $this->app->make(Reader::class),
                $this->app->make(Filesystem::class)
            );
        });

        $this->app->alias('excel', Excel::class);
        $this->app->alias('excel', Exporter::class);
        $this->app->alias('excel', Importer::class);

        Collection::mixin(new DownloadCollection);
        Collection::mixin(new StoreCollection);

        $this->commands([
            ExportMakeCommand::class,
            ImportMakeCommand::class,
        ]);
    }

    /**
     * @return string
     */
    protected function getConfigFile(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'excel.php';
    }
}
