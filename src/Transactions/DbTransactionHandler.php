<?php

namespace Omt\ExcelHelper\Transactions;

use Illuminate\Database\ConnectionInterface;

class DbTransactionHandler implements TransactionHandler
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param callable $callback
     *
     * @throws \Throwable
     * @return mixed
     */
    public function __invoke(callable $callback)
    {
        return $this->connection->transaction($callback);
    }
}
