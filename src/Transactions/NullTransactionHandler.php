<?php

namespace Omt\ExcelHelper\Transactions;

class NullTransactionHandler implements TransactionHandler
{
    /**
     * @param callable $callback
     *
     * @return mixed
     */
    public function __invoke(callable $callback)
    {
        return $callback();
    }
}
